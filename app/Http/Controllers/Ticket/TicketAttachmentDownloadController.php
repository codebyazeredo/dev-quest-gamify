<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\TicketAttachment;
use App\Support\AttachmentResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketAttachmentDownloadController extends Controller
{
    public function show(Request $request, TicketAttachment $attachment): Response
    {
        $this->authorize('viewAttachments', $attachment->ticket);

        if ($attachment->message?->is_internal) {
            $this->authorize('view', $attachment->ticket);
        }

        return AttachmentResponse::make($attachment, $request->boolean('download'));
    }
}
