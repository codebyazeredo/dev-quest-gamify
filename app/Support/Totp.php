<?php

namespace App\Support;

class Totp
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    private const PERIOD_SECONDS = 30;

    private const DIGITS = 6;

    private const TOLERATED_STEPS = 1;

    public static function generateSecret(): string
    {
        return self::base32Encode(random_bytes(20));
    }

    public static function currentStep(): int
    {
        return intdiv(time(), self::PERIOD_SECONDS);
    }

    public static function code(string $secret, int $step): string
    {
        $hash = hash_hmac('sha1', pack('J', $step), self::base32Decode($secret), true);
        $offset = ord($hash[19]) & 0x0F;
        $binary = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;

        return str_pad((string) ($binary % 10 ** self::DIGITS), self::DIGITS, '0', STR_PAD_LEFT);
    }

    /** Retorna o passo de tempo que validou o código, ou null quando o código é inválido. */
    public static function matchingStep(string $secret, string $code): ?int
    {
        $currentStep = self::currentStep();

        for ($step = $currentStep - self::TOLERATED_STEPS; $step <= $currentStep + self::TOLERATED_STEPS; $step++) {
            if (hash_equals(self::code($secret, $step), $code)) {
                return $step;
            }
        }

        return null;
    }

    public static function provisioningUri(string $secret, string $account, string $issuer): string
    {
        $label = rawurlencode("{$issuer}:{$account}");

        return "otpauth://totp/{$label}?secret={$secret}&issuer=".rawurlencode($issuer);
    }

    private static function base32Encode(string $bytes): string
    {
        $bits = '';
        foreach (str_split($bytes) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }

        return collect(str_split($bits, 5))
            ->map(fn (string $chunk) => self::BASE32_ALPHABET[bindec(str_pad($chunk, 5, '0'))])
            ->implode('');
    }

    private static function base32Decode(string $encoded): string
    {
        $bits = '';
        foreach (str_split(strtoupper($encoded)) as $character) {
            $bits .= str_pad(decbin(strpos(self::BASE32_ALPHABET, $character)), 5, '0', STR_PAD_LEFT);
        }

        return collect(str_split($bits, 8))
            ->filter(fn (string $chunk) => strlen($chunk) === 8)
            ->map(fn (string $chunk) => chr(bindec($chunk)))
            ->implode('');
    }
}
