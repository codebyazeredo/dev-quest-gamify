<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Paginação e filtros opcionais comuns às listagens administrativas. */
class PaginatedListRequest extends FormRequest
{
    private const DEFAULT_PER_PAGE = 10;

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'in:10,25,50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:active,inactive,running,scheduled,ended'],
            'unlock_type' => ['nullable', 'string', 'in:achievement,challenge,challenges_count'],
        ];
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? self::DEFAULT_PER_PAGE);
    }

    public function search(): string
    {
        return trim((string) $this->validated('search'));
    }

    public function status(): string
    {
        return (string) $this->validated('status');
    }

    public function unlockType(): string
    {
        return (string) $this->validated('unlock_type');
    }
}
