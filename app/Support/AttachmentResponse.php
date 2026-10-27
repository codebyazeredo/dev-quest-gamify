<?php

namespace App\Support;

use App\Models\TaskAttachment;
use App\Models\TicketAttachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentResponse
{
    public static function make(TaskAttachment|TicketAttachment $attachment, bool $forceDownload = false): StreamedResponse
    {
        $disk = Storage::disk(StoredFile::DISK);

        abort_unless($disk->exists($attachment->path), 404, 'O arquivo deste anexo não foi encontrado.');

        $headers = ['Content-Type' => $attachment->mime_type, 'X-Content-Type-Options' => 'nosniff'];

        if ($attachment->canBeViewedInline() && ! $forceDownload) {
            return $disk->response($attachment->path, $attachment->original_name, $headers);
        }

        return $disk->download($attachment->path, $attachment->original_name, $headers);
    }
}
