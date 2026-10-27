<?php

namespace App\Http\Requests\Gamification;

use Illuminate\Foundation\Http\FormRequest;

class RankingRequest extends FormRequest
{
    private const DEFAULT_ROLE = 'dev';

    private const DEFAULT_PERIOD = 'total';

    private const DEFAULT_PER_PAGE = 10;

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'role' => ['nullable', 'in:dev,tester,suporte'],
            'period' => ['nullable', 'in:week,month,total'],
            'porPagina' => ['nullable', 'integer', 'in:10,25,50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function role(): string
    {
        return $this->validated('role') ?? self::DEFAULT_ROLE;
    }

    public function period(): string
    {
        return $this->validated('period') ?? self::DEFAULT_PERIOD;
    }

    public function perPage(): int
    {
        return (int) ($this->validated('porPagina') ?? self::DEFAULT_PER_PAGE);
    }
}
