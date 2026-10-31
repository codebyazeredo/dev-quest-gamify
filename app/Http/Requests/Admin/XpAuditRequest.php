<?php

namespace App\Http\Requests\Admin;

use App\Enums\XpSourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class XpAuditRequest extends FormRequest
{
    private const DEFAULT_PER_PAGE = 10;

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'source' => ['nullable', 'integer', Rule::enum(XpSourceType::class)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'porPagina' => ['nullable', 'integer', 'in:10,25,50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['user_id' => 'usuário', 'source' => 'origem', 'from' => 'data inicial', 'to' => 'data final'];
    }

    /** @return array{user_id: ?int, source: ?int, from: ?string, to: ?string} */
    public function filters(): array
    {
        return [
            'user_id' => $this->integer('user_id') ?: null,
            'source' => $this->filled('source') ? $this->integer('source') : null,
            'from' => $this->validated('from'),
            'to' => $this->validated('to'),
        ];
    }

    public function perPage(): int
    {
        return (int) ($this->validated('porPagina') ?? self::DEFAULT_PER_PAGE);
    }
}
