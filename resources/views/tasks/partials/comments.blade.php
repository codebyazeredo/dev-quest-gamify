@php
    $canComment = auth()->user()->can('create', [\App\Models\TaskComment::class, $task]);
@endphp

<div x-data="taskComments({{ $task->id }})">
    <ul class="list-group list-group-flush">
        @forelse ($task->comments as $comment)
            <li class="list-group-item d-flex align-items-start gap-3">
                @if ($comment->user)
                    <x-avatar :user="$comment->user" />
                @endif

                <div class="min-w-0 flex-grow-1">
                    <div class="small">
                        <span class="fw-medium">{{ $comment->user?->name ?? 'Usuário removido' }}</span>
                        <span class="text-body-secondary">&middot; {{ $comment->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="mb-0" style="white-space: pre-line;">{{ $comment->body }}</p>
                </div>

                @can('delete', $comment)
                    <x-icon-button x-on:click="remove({{ $comment->id }})" icon="trash3" variant="danger" label="Remover comentário" />
                @endcan
            </li>
        @empty
            <li class="list-group-item small text-body-secondary">Nenhum comentário ainda.</li>
        @endforelse
    </ul>

    @if ($canComment)
        <form x-on:submit.prevent="add" class="p-3 border-top vstack gap-2">
            <label for="task-comment-body" class="visually-hidden">Comentário</label>
            <textarea id="task-comment-body" x-model="body" x-bind:class="{ 'is-invalid': firstError('body') }" class="form-control" rows="2" maxlength="2000" placeholder="Escreva um comentário..."></textarea>
            <div class="invalid-feedback" x-text="firstError('body')"></div>
            <div class="text-end">
                <x-button type="submit" x-bind:disabled="isBusy"><i class="bi bi-send" aria-hidden="true"></i> Comentar</x-button>
            </div>
        </form>
    @endif
</div>
