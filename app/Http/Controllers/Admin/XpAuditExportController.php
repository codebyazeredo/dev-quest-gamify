<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\XpAuditRequest;
use App\Models\User;
use App\Services\Admin\XpAuditService;
use App\Support\CsvDownload;
use Symfony\Component\HttpFoundation\StreamedResponse;

class XpAuditExportController extends Controller
{
    public function __construct(private XpAuditService $audit) {}

    public function __invoke(XpAuditRequest $request): StreamedResponse
    {
        $this->authorize('accessAdminPanel', User::class);

        return CsvDownload::make(
            'auditoria-xp-'.now()->format('Y-m-d').'.csv',
            ['Data', 'Usuário', 'XP', 'Origem', 'Motivo'],
            $this->audit->exportRows($request->filters()),
        );
    }
}
