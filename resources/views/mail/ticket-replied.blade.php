<x-mail::message>
# Nova resposta no chamado #{{ $ticket->id }}

**{{ $ticket->title }}**

{{ $reply->body }}

<x-mail::button :url="route('portal.tickets.show', $ticket)">
Abrir chamado
</x-mail::button>
</x-mail::message>
