<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsurePortalClient;
use App\Http\Requests\Portal\PortalLoginRequest;
use App\Services\PortalAuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortalAuthController extends Controller
{
    public function __construct(private PortalAuthService $auth) {}

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->session()->has(EnsurePortalClient::SESSION_KEY)) {
            return redirect()->route('portal.tickets.index');
        }

        return view('portal.login');
    }

    public function store(PortalLoginRequest $request): RedirectResponse
    {
        $client = $this->auth->attempt($request->validated('cnpj'), $request->validated('email'));

        if ($client === null) {
            return back()->withInput($request->only('cnpj'))->withErrors([
                'cnpj' => 'CNPJ ou e-mail não conferem com um cliente ativo. Fale com o suporte.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put(EnsurePortalClient::SESSION_KEY, $client->id);

        return redirect()->route('portal.tickets.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget(EnsurePortalClient::SESSION_KEY);
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
