<?php

namespace App\Services;

use App\Exceptions\DuplicateEntryException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Support\Totp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TwoFactorService
{
    private const RECOVERY_CODES_COUNT = 8;

    private const USED_STEP_TTL_SECONDS = 120;

    public function __construct(private UserRepository $users) {}

    public function beginSetup(User $user): string
    {
        if ($user->hasTwoFactorEnabled()) {
            throw new DuplicateEntryException('A autenticação em dois fatores já está ativa. Desative-a antes de configurar novamente.');
        }

        $secret = Totp::generateSecret();
        $this->users->update($user, ['two_factor_secret' => $secret, 'two_factor_confirmed_at' => null, 'two_factor_recovery_codes' => null]);

        return $secret;
    }

    /** @return array<int, string>|null códigos de recuperação em texto puro, ou null quando o código informado é inválido */
    public function confirmSetup(User $user, string $code): ?array
    {
        if ($user->two_factor_secret === null || $user->hasTwoFactorEnabled() || ! $this->consumeTotp($user, $code)) {
            return null;
        }

        $recoveryCodes = collect(range(1, self::RECOVERY_CODES_COUNT))
            ->map(fn () => Str::lower(Str::random(5).'-'.Str::random(5)))
            ->all();

        $this->users->update($user, ['two_factor_confirmed_at' => now(), 'two_factor_recovery_codes' => $recoveryCodes]);

        return $recoveryCodes;
    }

    public function disable(User $user): void
    {
        $this->users->update($user, ['two_factor_secret' => null, 'two_factor_recovery_codes' => null, 'two_factor_confirmed_at' => null]);
    }

    public function passesChallenge(User $user, string $code): bool
    {
        $code = trim($code);

        return $this->consumeTotp($user, $code) || $this->consumeRecoveryCode($user, Str::lower($code));
    }

    private function consumeTotp(User $user, string $code): bool
    {
        $step = Totp::matchingStep($user->two_factor_secret, $code);

        return $step !== null && Cache::add("two-factor-used:{$user->id}:{$step}", true, self::USED_STEP_TTL_SECONDS);
    }

    private function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];

        if (! in_array($code, $codes, true)) {
            return false;
        }

        $this->users->update($user, ['two_factor_recovery_codes' => array_values(array_diff($codes, [$code]))]);

        return true;
    }
}
