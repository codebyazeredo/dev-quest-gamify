<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\TicketListRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Support\CsvDownload;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketExportController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function __invoke(TicketListRequest $request): StreamedResponse
    {
        $this->authorize('viewAny', Ticket::class);

        return CsvDownload::make(
            'chamados-'.now()->format('Y-m-d').'.csv',
            ['Nº', 'Assunto', 'Cliente', 'Situação', 'Prioridade', 'Responsável', 'Aberto em', 'Prazo', '1ª resposta', 'Tarefa'],
            $this->tickets->exportRows($request->filters(), $request->user()),
        );
    }
}
