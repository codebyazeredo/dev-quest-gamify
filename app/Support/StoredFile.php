<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class StoredFile
{
    public const DISK = 'local';

    /** @return array{original_name: string, path: string, mime_type: string, size: int} */
    public static function store(UploadedFile $file, string $directory): array
    {
        return [
            'original_name' => $file->getClientOriginalName(),
            'path' => $file->store($directory, self::DISK),
            'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }
}
