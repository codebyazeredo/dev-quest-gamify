<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ConvertTicketRequest;
use App\Http\Requests\Ticket\TicketAssigneeRequest;
use App\Http\Requests\Ticket\TicketListRequest;
use App\Http\Requests\Ticket\TicketMessageRequest;
use App\Http\Requests\Ticket\TicketPriorityRequest;
use App\Http\Requests\Ticket\TicketStatusRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index(TicketListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Ticket::class);

        $page = $this->tickets->queue($request->filters(), $request->user(), $request->perPage());

        return TicketResource::collection($page)
            ->additional(['summary' => $this->tickets->queueSummary($request->user())])
            ->response();
    }

    public function updateStatus(TicketStatusRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $ticket = $this->tickets->changeStatus($ticket, $request->status());

        return response()->json(['message' => "Chamado #{$ticket->id} marcado como \"{$ticket->status->label()}\"."]);
    }

    public function updatePriority(TicketPriorityRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $ticket = $this->tickets->changePriority($ticket, $request->priority());

        return response()->json(['message' => "Prioridade do chamado #{$ticket->id} alterada para \"{$ticket->priority->label()}\"."]);
    }

    public function updateAssignee(TicketAssigneeRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $ticket = $this->tickets->assign($ticket, $request->assigneeId())->load('assignedTo');

        return response()->json(['message' => $ticket->assignedTo !== null
            ? "Chamado #{$ticket->id} atribuído a {$ticket->assignedTo->name}."
            : "Chamado #{$ticket->id} ficou sem responsável."]);
    }

    public function claim(Request $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $ticket = $this->tickets->assign($ticket, $request->user()->id);

        return response()->json(['message' => "Você assumiu o chamado #{$ticket->id}."]);
    }

    public function reply(TicketMessageRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $this->tickets->reply($ticket, $request->validated('body'), $request->user(), $request->file('files', []), $request->isInternal());

        return response()->json([
            'message' => $request->isInternal() ? 'Nota interna adicionada. O cliente não a verá.' : 'Resposta enviada ao cliente.',
        ], 201);
    }

    public function convertToTask(ConvertTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $task = $this->tickets->convertToTask($ticket, $request->validated(), $request->user());

        return response()->json(['message' => "Tarefa DQ-{$task->id} criada a partir do chamado."], 201);
    }
}
