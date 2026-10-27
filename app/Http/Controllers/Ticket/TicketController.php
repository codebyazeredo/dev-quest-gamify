<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Requests\Ticket\ConvertTicketRequest;
use App\Http\Requests\Ticket\TicketMessageRequest;
use App\Http\Requests\Ticket\TicketStatusRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Ticket::class);

        $page = $this->tickets->paginateForStaff($request->perPage(), $request->status(), $request->search());

        return TicketResource::collection($page)->response();
    }

    public function updateStatus(TicketStatusRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $ticket = $this->tickets->changeStatus($ticket, $request->status());

        return response()->json(['message' => "Chamado #{$ticket->id} marcado como \"{$ticket->status->label()}\"."]);
    }

    public function reply(TicketMessageRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $this->tickets->reply($ticket, $request->validated('body'), $request->user());

        return response()->json(['message' => 'Resposta enviada ao cliente.'], 201);
    }

    public function convertToTask(ConvertTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $task = $this->tickets->convertToTask($ticket, $request->validated(), $request->user());

        return response()->json(['message' => "Tarefa DQ-{$task->id} criada a partir do chamado."], 201);
    }
}
