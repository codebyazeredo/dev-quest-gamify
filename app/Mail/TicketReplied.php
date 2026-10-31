<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplied extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public TicketMessage $reply) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Resposta no chamado #{$this->ticket->id}: {$this->ticket->title}");
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.ticket-replied');
    }
}
