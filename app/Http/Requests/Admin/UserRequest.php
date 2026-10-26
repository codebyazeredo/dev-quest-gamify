<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Valida criação (vincula uma pessoa e exige senha) e edição (senha opcional) de usuário. */
class UserRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $isCreation = $this->isMethod('POST');

        return [
            'person_id' => $isCreation ? ['required', 'integer', Rule::exists('people', 'id')] : [],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => [$isCreation ? 'required' : 'nullable', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::exists('roles', 'name')],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['person_id' => 'pessoa', 'email' => 'e-mail', 'password' => 'senha', 'roles' => 'roles'];
    }
}
