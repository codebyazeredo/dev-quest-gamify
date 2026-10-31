<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\TwoFactorService;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorSettingsController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $pendingSecret = $user->hasTwoFactorEnabled() ? null : $user->two_factor_secret;

        return view('profile.security', [
            'pendingSecret' => $pendingSecret,
            'provisioningUri' => $pendingSecret !== null
                ? Totp::provisioningUri($pendingSecret, $user->email, AppSetting::current()->displayName())
                : null,
            'recoveryCodes' => $request->session()->get('recovery_codes'),
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        if ($request->user()->hasTwoFactorEnabled()) {
            return back()->withErrors(['two_factor' => 'A autenticação em dois fatores já está ativa. Desative-a antes de configurar novamente.']);
        }

        $this->twoFactor->beginSetup($request->user());

        return redirect()->route('security');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'digits:6']]);

        $recoveryCodes = $this->twoFactor->confirmSetup($request->user(), $request->string('code')->toString());

        if ($recoveryCodes === null) {
            return back()->withErrors(['code' => 'O código informado é inválido ou expirou. Confira o horário do seu celular e tente novamente.']);
        }

        return redirect()->route('security')
            ->with('recovery_codes', $recoveryCodes)
            ->with('status', 'Autenticação em dois fatores ativada.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'string', 'current_password']]);

        $this->twoFactor->disable($request->user());

        return redirect()->route('security')->with('status', 'Autenticação em dois fatores desativada.');
    }
}
