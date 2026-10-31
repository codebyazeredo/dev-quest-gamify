@php
    $canUpload = auth()->user()->can('create', [\App\Models\TaskAttachment::class, $task]);
@endphp

<div x-data="attachmentUploader({{ $task->id }}, @js($canUpload))" x-on:paste.window="pasteFiles($event)">
    @if ($canUpload)
        <div class="p-3 border-bottom">
            <x-file-input x-ref="picker" aria-label="Anexos" />
            <div class="text-danger small mt-2" x-show="firstError('files') || firstError('files.0')" x-cloak x-text="firstError('files') || firstError('files.0')"></div>
            <div class="text-end mt-2">
                <x-button type="button" x-on:click="upload" x-bind:disabled="isBusy"><i class="bi bi-cloud-arrow-up" aria-hidden="true"></i> Enviar anexos</x-button>
            </div>
        </div>
    @endif

    <ul class="list-group list-group-flush">
        @forelse ($task->attachments as $attachment)
            <li class="list-group-item d-flex align-items-center gap-3">
                @if ($attachment->isImage())
                    <a href="{{ route('attachments.show', $attachment) }}" target="_blank" rel="noopener noreferrer" class="flex-shrink-0">
                        <img src="{{ route('attachments.show', $attachment) }}" alt="{{ $attachment->original_name }}" class="attachment-thumb" loading="lazy">
                    </a>
                @else
                    <span class="attachment-icon flex-shrink-0"><i class="bi {{ $attachment->icon() }}" aria-hidden="true"></i></span>
                @endif

                <div class="min-w-0 flex-grow-1">
                    <a href="{{ route('attachments.show', $attachment) }}" target="_blank" rel="noopener noreferrer" class="d-block fw-medium text-body text-truncate">{{ $attachment->original_name }}</a>
                    <div class="small text-body-secondary">{{ $attachment->human_size }} &middot; {{ $attachment->user?->name ?? 'Usuário removido' }} &middot; {{ $attachment->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="d-flex flex-shrink-0 gap-1">
                    <x-icon-button :href="route('attachments.show', [$attachment, 'download' => 1])" icon="download" label="Baixar" />

                    @can('delete', $attachment)
                        <x-icon-button x-on:click="remove({{ $attachment->id }}, @js($attachment->original_name))" icon="trash3" variant="danger" label="Remover anexo" />
                    @endcan
                </div>
            </li>
        @empty
            <li class="list-group-item small text-body-secondary">Nenhum anexo ainda.</li>
        @endforelse
    </ul>
</div>
