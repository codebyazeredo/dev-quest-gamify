<?php

namespace App\Http\Controllers\Admin;

use App\Enums\XpSourceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\XpAuditRequest;
use App\Models\User;
use App\Services\Admin\XpAuditService;
use Illuminate\Contracts\View\View;

class XpAuditPageController extends Controller
{
    public function __construct(private XpAuditService $audit) {}

    public function index(XpAuditRequest $request): View
    {
        $this->authorize('accessAdminPanel', User::class);

        $result = $this->audit->audit($request->filters(), $request->perPage());

        return view('admin.xp-audit', [
            ...$result,
            'transactions' => $result['transactions']->withQueryString(),
            'filters' => $request->filters(),
            'sources' => XpSourceType::cases(),
        ]);
    }
}
