<?php

namespace App\Http\Requests\Admin;

use App\Rules\ValidCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida criação e edição de cliente; o CNPJ é único mesmo com ou sem máscara. */
class ClientRequest extends FormRequest
{
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
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['razao_social' => 'razão social', 'cnpj' => 'CNPJ', 'email' => 'e-mail', 'is_active' => 'ativo'];
    }
}
