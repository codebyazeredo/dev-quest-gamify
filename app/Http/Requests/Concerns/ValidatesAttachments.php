<?php

namespace App\Http\Requests\Concerns;

trait ValidatesAttachments
{
    private const MAX_FILES = 10;

    private const MAX_SIZE_KB = 10240;

    private const ALLOWED_TYPES = 'jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip';

    /** @return array<string, array<int, string>> */
    protected function attachmentRules(bool $required): array
    {
        return [
            'files' => $required
                ? ['required', 'array', 'min:1', 'max:'.self::MAX_FILES]
                : ['nullable', 'array', 'max:'.self::MAX_FILES],
            'files.*' => ['file', 'max:'.self::MAX_SIZE_KB, 'mimes:'.self::ALLOWED_TYPES],
        ];
    }

    /** @return array<string, string> */
    protected function attachmentAttributes(): array
    {
        return ['files' => 'anexos', 'files.*' => 'anexo'];
    }

    /** @return array<string, string> */
    protected function attachmentMessages(): array
    {
        return [
            'files.*.mimes' => 'O :attribute deve ser imagem, PDF, documento do Office, texto, CSV ou ZIP.',
            'files.*.max' => 'O :attribute não pode ter mais de 10 MB.',
            'files.max' => 'Envie no máximo '.self::MAX_FILES.' anexos por vez.',
        ];
    }
}
