<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Contracts\View\View;

class TicketPageController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index(): View
    {
        $this->authorize('viewAny', Ticket::class);

        return view('tickets.index');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'client.address.country',
            'client.businessSector',
            'attachments',
            'messages.attachments',
            'messages.user',
            'task.column',
            'task.assignedTo',
            'task.board.columns',
        ]);

        $boards = Board::query()->where('is_active', true)->with('columns')->orderBy('name')->get();

        return view('tickets.show', [
            'ticket' => $ticket,
            'progress' => $this->tickets->progressFor($ticket),
            'boards' => $boards->map(fn (Board $board) => [
                'id' => $board->id,
                'name' => $board->name,
                'columns' => $board->columns->map(fn ($column) => ['id' => $column->id, 'name' => $column->name])->all(),
            ])->all(),
            'categories' => TaskCategory::query()->orderBy('name')->get(),
            'priorities' => TaskPriority::query()->orderBy('multiplier')->get(),
        ]);
    }
}
