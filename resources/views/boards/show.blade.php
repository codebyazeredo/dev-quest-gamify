<x-layouts.app>
    <div
        class="d-flex flex-column flex-grow-1 min-h-0"
        x-data="modalForm"
        x-on:open-board-form.window="open($event.detail)"
        x-on:close-modal="close"
    >
        <x-page-header :title="$board->name" :subtitle="$board->description" :back="route('boards.index')" backLabel="Quadros">
            <x-icon-button :href="route('boards.archive', $board)" icon="archive" label="Arquivo do quadro" />

            @can('update', $board)
                <x-icon-button
                    x-on:click="$dispatch('open-board-form', { url: '/api/boards/{{ $board->id }}', method: 'put', form: {{ \Illuminate\Support\Js::from(['name' => $board->name, 'description' => (string) $board->description, 'is_active' => $board->is_active]) }} })"
                    icon="pencil-square"
                    label="Editar quadro"
                />
            @endcan
        </x-page-header>

        @include('boards.partials.kanban')

        <template x-if="isOpen">
            <x-modal title="Editar quadro" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" required />
                    <x-form-textarea name="description" label="Descrição" rows="2" />
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
