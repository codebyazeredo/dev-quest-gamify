<?php

namespace App\Http\Requests\Admin;

use App\Enums\Gender;
use App\Rules\ValidCpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida criação e edição de pessoa (dados pessoais, foto e endereço). */
class PersonRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $person = $this->route('person');

        return [
            'nome' => ['required', 'string', 'max:150'],
            'cpf' => ['required', 'string', 'max:14', new ValidCpf, Rule::unique('people', 'cpf')->ignore($person)],
            'rg' => ['nullable', 'string', 'max:20', Rule::unique('people', 'rg')->ignore($person)],
            'nascimento' => ['required', 'date'],
            'sexo' => ['required', Rule::enum(Gender::class)],
            'email' => ['required', 'email', 'max:255'],
            'telefone1' => ['required', 'string', 'max:20'],
            'telefone2' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'cep' => ['required', 'string', 'max:9'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'cidade' => ['required', 'string', 'max:120'],
            'estado' => ['required', 'string', 'size:2'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['foto' => 'foto', 'cep' => 'CEP', 'estado' => 'estado (UF)'];
    }
}
