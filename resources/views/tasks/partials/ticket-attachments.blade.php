@php
    $ticket = $task->ticket;
    $messagesWithAttachments = $ticket?->messages->reject->is_internal->filter(fn ($message) => $message->attachments->isNotEmpty()) ?? collect();
@endphp

@if ($ticket && ($ticket->attachments->isNotEmpty() || $messagesWithAttachments->isNotEmpty()))
    <div class="border-top">
        <h3 class="h6 px-3 pt-3 mb-0"><i class="bi bi-headset me-1" aria-hidden="true"></i> Anexos do chamado #{{ $ticket->id }}</h3>

        <ul class="list-group list-group-flush">
            @if ($ticket->attachments->isNotEmpty())
                <li class="list-group-item">
                    <div class="small fw-medium">Abertura do chamado <span class="text-body-secondary fw-normal">&middot; {{ $ticket->client->razao_social }} &middot; {{ $ticket->created_at->format('d/m/Y H:i') }}</span></div>
                    <x-attachment-chips :attachments="$ticket->attachments" route="ticket-attachments.show" />
                </li>
            @endif

            @foreach ($messagesWithAttachments as $message)
                <li class="list-group-item">
                    <div class="small fw-medium">
                        Resposta de {{ $message->isFromStaff() ? $message->user->name : $ticket->client->razao_social }}
                        <span class="text-body-secondary fw-normal">&middot; {{ $message->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <x-attachment-chips :attachments="$message->attachments" route="ticket-attachments.show" />
                </li>
            @endforeach
        </ul>
    </div>
@endif
