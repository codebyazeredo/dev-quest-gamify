<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\TaskAttachment;
use App\Support\AttachmentResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskAttachmentDownloadController extends Controller
{
    public function show(Request $request, TaskAttachment $attachment): StreamedResponse
    {
        $this->authorize('view', $attachment);

        return AttachmentResponse::make($attachment, $request->boolean('download'));
    }
}
