<?php

namespace App\Http\Requests\Concerns;

trait ValidatesAttachments
{
    /** @return array<string, array<int, string>> */
    protected function attachmentRules(bool $required): array
    {
        $maxFiles = config('attachments.max_files');

        return [
            'files' => $required
                ? ['required', 'array', 'min:1', 'max:'.$maxFiles]
                : ['nullable', 'array', 'max:'.$maxFiles],
            'files.*' => ['file', 'max:'.config('attachments.max_size_kb'), 'mimes:'.implode(',', config('attachments.extensions'))],
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
        $maxSizeMb = config('attachments.max_size_kb') / 1024;

        return [
            'files.*.mimes' => 'O :attribute deve ser imagem, vídeo, PDF, documento do Office, texto, CSV ou ZIP.',
            'files.*.max' => "O :attribute não pode ter mais de {$maxSizeMb} MB.",
            'files.max' => 'Envie no máximo '.config('attachments.max_files').' anexos por vez.',
        ];
    }
}
