<x-layouts.app>
    <div
        class="d-flex flex-column flex-grow-1 min-h-0"
        x-data="modalForm"
        x-on:open-board-form.window="open($event.detail)"
        x-on:close-modal="close"
    >
        <x-page-header :title="$board->name" :subtitle="$board->description" :back="route('boards.index')" backLabel="Quadros" />

        @include('boards.partials.kanban')

        <template x-if="isOpen">
            <x-modal title="Editar quadro" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" required />
                    <x-form-textarea name="description" label="Descrição" rows="2" />
                    <x-form-input name="github_repository" label="Repositório do GitHub" placeholder="dono/repositorio" />
                    <x-form-switch name="is_active" label="Ativo" />

                    <div class="d-flex justify-content-end gap-2">
                        <x-button variant="secondary" x-on:click="close">Fechar</x-button>
                        <x-button type="submit" x-bind:disabled="isSaving">Salvar</x-button>
                    </div>
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
