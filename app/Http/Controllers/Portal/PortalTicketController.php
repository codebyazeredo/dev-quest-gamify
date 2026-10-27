<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\OpenTicketRequest;
use App\Http\Requests\Ticket\TicketMessageRequest;
use App\Models\Client;
use App\Services\TicketService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortalTicketController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(private TicketService $tickets) {}

    public function index(Request $request): View
    {
        $client = $this->client($request);

        return view('portal.tickets.index', [
            'client' => $client,
            'tickets' => $this->tickets->paginateForClient($client, self::PER_PAGE),
        ]);
    }

    public function store(OpenTicketRequest $request): RedirectResponse
    {
        $ticket = $this->tickets->open($this->client($request), $request->validated());

        return redirect()->route('portal.tickets.show', $ticket)->with('status', "Chamado #{$ticket->id} aberto. Responderemos por aqui.");
    }

    public function show(Request $request, int $ticket): View
    {
        $client = $this->client($request);

        return view('portal.tickets.show', [
            'client' => $client,
            'ticket' => $this->tickets->findForClient($client, $ticket),
        ]);
    }

    public function reply(TicketMessageRequest $request, int $ticket): RedirectResponse
    {
        $found = $this->tickets->findForClient($this->client($request), $ticket);

        $this->tickets->reply($found, $request->validated('body'));

        return redirect()->route('portal.tickets.show', $found)->with('status', 'Mensagem enviada.');
    }

    private function client(Request $request): Client
    {
        return $request->attributes->get('portal_client');
    }
}
