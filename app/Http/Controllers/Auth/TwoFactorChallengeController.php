<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\BoardService;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public const SESSION_KEY = 'two_factor_challenge';

    public const CHALLENGE_MINUTES = 5;

    private const MAX_ATTEMPTS = 5;

    public function __construct(
        private TwoFactorService $twoFactor,
        private UserRepository $users,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        return $this->pendingUser($request) === null
            ? $this->backToLogin($request)
            : view('auth.two-factor-challenge');
    }

    public function store(Request $request, BoardService $boards): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:20']]);

        $user = $this->pendingUser($request);

        if ($user === null) {
            return $this->backToLogin($request);
        }

        $throttleKey = "two-factor:{$user->id}|{$request->ip()}";

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $request->session()->forget(self::SESSION_KEY);

            throw ValidationException::withMessages([
                'email' => 'Muitas tentativas de código incorretas. Entre com e-mail e senha novamente em alguns minutos.',
            ])->redirectTo(route('login'));
        }

        if (! $this->twoFactor->passesChallenge($user, $request->string('code')->toString())) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages(['code' => 'O código informado é inválido ou já foi utilizado.']);
        }

        RateLimiter::clear($throttleKey);

        $remember = $request->session()->pull(self::SESSION_KEY)['remember'];

        Auth::login($user, $remember);

        $request->session()->regenerate();

        return redirect()->intended($boards->landingUrl());
    }

    private function pendingUser(Request $request): ?User
    {
        $challenge = $request->session()->get(self::SESSION_KEY);

        if ($challenge === null || $challenge['expires_at'] < now()->timestamp) {
            return null;
        }

        $user = $this->users->findOrFail($challenge['user_id']);

        return $user->hasTwoFactorEnabled() ? $user : null;
    }

    private function backToLogin(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('login')->withErrors([
            'email' => 'A verificação em dois fatores expirou. Entre com e-mail e senha novamente.',
        ]);
    }
}
