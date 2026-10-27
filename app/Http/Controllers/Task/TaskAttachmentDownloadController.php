<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskAttachmentDownloadController extends Controller
{
    public function show(Request $request, TaskAttachment $attachment): StreamedResponse
    {
        $this->authorize('view', $attachment);

        $disk = Storage::disk(TaskAttachment::DISK);

        abort_unless($disk->exists($attachment->path), 404, 'O arquivo deste anexo não foi encontrado.');

        $headers = ['Content-Type' => $attachment->mime_type, 'X-Content-Type-Options' => 'nosniff'];

        if ($attachment->canBeViewedInline() && ! $request->boolean('download')) {
            return $disk->response($attachment->path, $attachment->original_name, $headers);
        }

        return $disk->download($attachment->path, $attachment->original_name, $headers);
    }
}
