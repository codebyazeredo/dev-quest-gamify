<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesAddress;
use App\Rules\ValidCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    use ValidatesAddress;

    protected function prepareForValidation(): void
    {
        if (is_string($this->cnpj)) {
            $this->merge(['cnpj' => preg_replace('/\D/', '', $this->cnpj)]);
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'razao_social' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', new ValidCnpj, Rule::unique('clients', 'cnpj')->ignore($this->route('client'))],
            'email' => ['required', 'email', 'max:255'],
            'business_sector_id' => ['nullable', 'integer', 'exists:business_sectors,id'],
            'is_active' => ['nullable', 'boolean'],
            ...$this->addressRules(required: false),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'razao_social' => 'razão social',
            'cnpj' => 'CNPJ',
            'email' => 'e-mail',
            'business_sector_id' => 'ramo de atuação',
            'is_active' => 'ativo',
            ...$this->addressAttributes(),
        ];
    }
}
