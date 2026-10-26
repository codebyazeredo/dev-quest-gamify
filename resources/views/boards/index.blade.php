<x-layouts.app>
    <div x-data="modalForm" x-on:open-board-form.window="open($event.detail)" x-on:close-modal="close">
        <x-page-header title="Quadros" subtitle="Escolha um quadro para acompanhar as tarefas do time.">
            @can('create', \App\Models\Board::class)
                <x-button x-on:click="$dispatch('open-board-form', { url: '/api/boards', method: 'post', form: { name: '', description: '', is_active: true, seed_default_columns: true } })">
                    <x-icon name="plus" /> Novo quadro
                </x-button>
            @endcan
        </x-page-header>

        <div class="row g-3">
            @forelse ($boards as $board)
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{{ route('boards.show', $board) }}" class="card card-hover h-100 text-decoration-none text-body">
                        <div class="card-body d-flex flex-column gap-3">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <x-icon-chip icon="columns" />
                                @unless ($board->is_active)
                                    <x-badge>Inativo</x-badge>
                                @endunless
                            </div>

                            <div>
                                <h2 class="h5 mb-1">{{ $board->name }}</h2>
                                <p class="small text-body-secondary mb-0">{{ $board->description ?: 'Sem descrição.' }}</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top small text-body-secondary">
                                <span><i class="bi bi-card-checklist me-1" aria-hidden="true"></i>{{ $board->tasks_count }} {{ $board->tasks_count === 1 ? 'tarefa' : 'tarefas' }}</span>
                                <span class="text-primary fw-medium">Abrir quadro <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center text-body-secondary py-5">
                        <i class="bi bi-kanban fs-1 d-block mb-2" aria-hidden="true"></i>
                        Nenhum quadro disponível.
                    </div>
                </div>
            @endforelse
        </div>

        <template x-if="isOpen">
            <x-modal title="Novo quadro">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" required autofocus />
                    <x-form-textarea name="description" label="Descrição" />
                    <x-form-switch name="is_active" label="Ativo" />

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="seed_default_columns" x-model="form.seed_default_columns">
                        <label class="form-check-label" for="seed_default_columns">
                            Criar colunas padrão de desenvolvimento
                            <span class="d-block small text-body-secondary">Backlog, A Fazer, Em Andamento, Em Revisão, Em Teste, Aprovado, Concluído</span>
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-button variant="secondary" x-on:click="close">Cancelar</x-button>
                        <x-button type="submit" x-bind:disabled="isSaving">Criar quadro</x-button>
                    </div>
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
