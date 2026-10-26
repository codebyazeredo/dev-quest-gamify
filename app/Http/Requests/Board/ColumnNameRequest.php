<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;

/** Nome de uma coluna do quadro, na criação ou ao renomear. */
class ColumnNameRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['name' => 'nome da coluna'];
    }
}
