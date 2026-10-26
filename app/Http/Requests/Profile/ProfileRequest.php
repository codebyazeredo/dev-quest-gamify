<?php

namespace App\Http\Requests\Profile;

use App\Enums\Gender;
use App\Rules\ValidCpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Valida a edição dos próprios dados; o endereço só é obrigatório para quem já tem um cadastrado. */
class ProfileRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $user = $this->user();
        $person = $user->person;
        $addressRule = $person?->address()->exists() ? 'required' : 'nullable';

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
            'cep' => [$addressRule, 'string', 'max:9'],
            'logradouro' => [$addressRule, 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'cidade' => [$addressRule, 'string', 'max:120'],
            'estado' => [$addressRule, 'string', 'size:2'],
            'login_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['login_email' => 'e-mail de acesso', 'password' => 'senha', 'cep' => 'CEP', 'estado' => 'estado (UF)'];
    }
}
