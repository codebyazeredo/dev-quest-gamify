<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskAttachmentsRequest extends FormRequest
{
    private const MAX_FILES = 10;

    private const MAX_SIZE_KB = 10240;

    private const ALLOWED_TYPES = 'jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip';

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:'.self::MAX_FILES],
            'files.*' => ['file', 'max:'.self::MAX_SIZE_KB, 'mimes:'.self::ALLOWED_TYPES],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['files' => 'anexos', 'files.*' => 'anexo'];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'files.*.mimes' => 'O :attribute deve ser imagem, PDF, documento do Office, texto, CSV ou ZIP.',
            'files.*.max' => 'O :attribute não pode ter mais de 10 MB.',
            'files.max' => 'Envie no máximo '.self::MAX_FILES.' anexos por vez.',
        ];
    }
}
