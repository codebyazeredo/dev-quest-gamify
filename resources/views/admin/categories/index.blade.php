<x-layouts.app>
    <div
        x-data="crudTable({ endpoint: '/api/categories', emptyForm: { name: '', base_points: 10, color: '#e2e5ea', text_color: '#1b2733' } })"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Categorias" subtitle="Tipos de tarefa e as cores exibidas no Kanban." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\TaskCategory::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Nova categoria</x-button>
            @endcan
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Categoria</th>
                            <th>Pontos base</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4">
                                    <span class="badge rounded-pill fw-medium px-3 py-2" x-text="row.name" x-bind:style="`background-color: ${row.color}; color: ${row.text_color};`"></span>
                                </td>
                                <td x-text="row.base_points"></td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" x-on:click="openEdit(row)" title="Editar" aria-label="Editar" class="btn btn-ghost btn-icon btn-icon-sm"><x-icon name="pencil" /></button>
                                        <button type="button" x-on:click="remove(row, 'Excluir esta categoria?')" title="Excluir" aria-label="Excluir" class="btn btn-ghost btn-ghost-danger btn-icon btn-icon-sm"><x-icon name="trash" /></button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="3" class="text-center text-body-secondary py-5">Nenhuma categoria cadastrada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Categoria">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <div>
                        <label for="name" class="form-label fw-medium small mb-1">Nome</label>
                        <input id="name" type="text" x-model="form.name" x-bind:class="{ 'is-invalid': firstError('name') }" class="form-control" autofocus>
                        <div class="invalid-feedback" x-text="firstError('name')"></div>
                    </div>

                    <div>
                        <label for="base_points" class="form-label fw-medium small mb-1">Pontos base</label>
                        <input id="base_points" type="number" min="0" x-model.number="form.base_points" x-bind:class="{ 'is-invalid': firstError('base_points') }" class="form-control">
                        <div class="invalid-feedback" x-text="firstError('base_points')"></div>
                    </div>

                    <div>
                        <span class="form-label fw-medium small d-block mb-0">Cor da categoria</span>
                        <div class="form-text mt-0 mb-2">Usada no card da tarefa no Kanban — a cor da fonte já vem ajustada para ficar legível.</div>

                        <div class="d-flex flex-wrap gap-2">
                            @foreach (\App\Models\TaskCategory::COLOR_PRESETS as $pair)
                                <button
                                    type="button"
                                    x-on:click="form.color = '{{ $pair['bg'] }}'; form.text_color = '{{ $pair['text'] }}'"
                                    title="{{ $pair['label'] }}"
                                    aria-label="{{ $pair['label'] }}"
                                    x-bind:aria-pressed="form.color === '{{ $pair['bg'] }}'"
                                    x-bind:class="form.color === '{{ $pair['bg'] }}' ? 'border-primary shadow-sm' : 'border-light-subtle'"
                                    class="btn btn-icon fw-bold border-2"
                                    style="background-color: {{ $pair['bg'] }}; color: {{ $pair['text'] }}; width: 2.5rem; height: 2.5rem;"
                                >Aa</button>
                            @endforeach
                        </div>
                        <div class="text-danger small mt-1" x-text="firstError('color')"></div>

                        <div class="rounded-3 border p-3 mt-3 fw-medium" x-bind:style="`background-color: ${form.color}; color: ${form.text_color};`">
                            Pré-visualização: <span x-text="form.name || 'Nome da categoria'"></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-button variant="secondary" x-on:click="closeModal">Cancelar</x-button>
                        <x-button type="submit" x-text="editingRow ? 'Salvar alterações' : 'Criar categoria'"></x-button>
                    </div>
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
