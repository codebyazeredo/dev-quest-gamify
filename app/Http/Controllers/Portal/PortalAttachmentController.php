<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TicketAttachment;
use App\Support\AttachmentResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortalAttachmentController extends Controller
{
    public function show(Request $request, TicketAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->ticket->client_id === $request->attributes->get('portal_client')->id, 404);

        return AttachmentResponse::make($attachment, $request->boolean('download'));
    }
}
