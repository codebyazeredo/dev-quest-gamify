<?php

namespace App\Http\Requests\Portal;

use App\Rules\ValidCnpj;
use Illuminate\Foundation\Http\FormRequest;

class PortalLoginRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'cnpj' => ['required', 'string', new ValidCnpj],
            'email' => ['required', 'email'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['cnpj' => 'CNPJ', 'email' => 'e-mail'];
    }
}
