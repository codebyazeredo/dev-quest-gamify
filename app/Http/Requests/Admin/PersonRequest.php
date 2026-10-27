<?php

namespace App\Http\Requests\Admin;

use App\Enums\Gender;
use App\Http\Requests\Concerns\ValidatesAddress;
use App\Rules\ValidCpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonRequest extends FormRequest
{
    use ValidatesAddress;

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
            ...$this->addressRules(required: true),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['foto' => 'foto', ...$this->addressAttributes()];
    }
}
