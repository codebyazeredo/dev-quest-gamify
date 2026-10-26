<?php

namespace App\Http\Requests\Admin;

use App\Enums\LogoSize;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppSettingRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'company_name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_size' => ['required', Rule::enum(LogoSize::class)],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'primary_color.required' => 'Escolha a cor primária do sistema.',
            'primary_color.regex' => 'A cor primária deve ser um hexadecimal válido no formato #RRGGBB (ex.: #0d6efd).',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['company_name' => 'nome da empresa', 'logo' => 'logo', 'logo_size' => 'tamanho da logo'];
    }
}
