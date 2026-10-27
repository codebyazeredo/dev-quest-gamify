@php
    $canUpload = auth()->user()->can('create', [\App\Models\TaskAttachment::class, $task]);
@endphp

<div class="card" x-data="attachmentUploader({{ $task->id }}, @js($canUpload))" x-on:paste.window="pasteFiles($event)">
    <div class="card-header bg-body fw-semibold d-flex align-items-center justify-content-between">
        <span><i class="bi bi-paperclip me-1" aria-hidden="true"></i> Anexos <x-badge>{{ $task->attachments->count() }}</x-badge></span>
    </div>

    @if ($canUpload)
        <div class="p-3 pb-0">
            <div
                class="attachment-dropzone"
                x-bind:class="{ 'is-dragging': isDragging, 'is-busy': isBusy }"
                x-on:dragover.prevent="isDragging = true"
                x-on:dragleave.prevent="isDragging = false"
                x-on:drop.prevent="dropFiles($event)"
                x-on:click="$refs.picker.click()"
                x-on:keydown.enter.prevent="$refs.picker.click()"
                role="button"
                tabindex="0"
            >
                <i class="bi bi-cloud-arrow-up fs-4" aria-hidden="true"></i>
                <span class="fw-medium" x-text="isBusy ? 'Enviando...' : 'Arraste arquivos, clique para escolher ou cole uma imagem (Ctrl+V)'"></span>
                <span class="small text-body-secondary">Imagens, PDF, documentos, planilhas, texto e ZIP · até 10 MB cada</span>
                <input type="file" multiple hidden x-ref="picker" x-on:change="pickFiles($event)">
            </div>
            <div class="text-danger small mt-2" x-show="firstError('files') || firstError('files.0')" x-cloak x-text="firstError('files') || firstError('files.0')"></div>
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
