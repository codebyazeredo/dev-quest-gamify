<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\TicketAttachment;
use App\Support\AttachmentResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketAttachmentDownloadController extends Controller
{
    public function show(Request $request, TicketAttachment $attachment): StreamedResponse
    {
        $this->authorize('view', $attachment->ticket);

        return AttachmentResponse::make($attachment, $request->boolean('download'));
    }
}
