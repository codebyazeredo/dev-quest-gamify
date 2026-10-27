<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Ticket;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use Illuminate\Contracts\View\View;

class TicketPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Ticket::class);

        return view('tickets.index');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        return view('tickets.show', [
            'ticket' => $ticket->load(['client', 'task', 'messages.user']),
            'boards' => Board::query()->where('is_active', true)->with('columns')->orderBy('name')->get(),
            'categories' => TaskCategory::query()->orderBy('name')->get(),
            'priorities' => TaskPriority::query()->orderBy('multiplier')->get(),
        ]);
    }
}
