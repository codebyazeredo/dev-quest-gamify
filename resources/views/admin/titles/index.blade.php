<x-layouts.app>
    <div
        x-data="crudTable({
            endpoint: '/api/titles',
            filters: { search: '', unlock_type: '', status: '' },
            emptyForm: { name: '', description: '', icon: 'medal', unlock_type: 'achievement', achievement_id: '', challenge_id: '', challenges_required: '', active: true },
        })"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Títulos" subtitle="Títulos desbloqueáveis por conquistas e desafios." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\Title::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Novo título</x-button>
            @endcan
        </x-page-header>

        <x-list-filters placeholder="Buscar títulos">
            <select x-model="filters.unlock_type" x-on:change="applyFilters" class="form-select w-auto" aria-label="Regra de desbloqueio">
                <option value="">Todas as regras</option>
                @foreach ($unlockTypes as $unlockType)
                    <option value="{{ $unlockType->value }}">{{ $unlockType->label() }}</option>
                @endforeach
            </select>

            <select x-model="filters.status" x-on:change="applyFilters" class="form-select w-auto" aria-label="Situação">
                <option value="">Ativos e inativos</option>
                <option value="active">Somente ativos</option>
                <option value="inactive">Somente inativos</option>
            </select>
        </x-list-filters>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Título</th>
                            <th>Como desbloquear</th>
                            <th>Desbloqueios</th>
                            <th>Status</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <x-icon-chip-js icon="row.icon" color="warning" size="sm" />
                                        <span class="fw-medium" x-text="row.name"></span>
                                    </div>
                                </td>
                                <td class="text-body-secondary" x-text="row.unlock_rule"></td>
                                <td x-text="row.unlocks_count"></td>
                                <td>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" aria-label="Ativo" x-bind:checked="row.active" x-on:change="postAction(row, 'toggle-active')">
                                    </div>
                                </td>
                                <td class="text-end pe-4"><x-row-actions-js confirm="Excluir este título?" /></td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="5" class="text-center text-body-secondary py-5">Nenhum título encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Título" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" autofocus />
                    <x-form-textarea name="description" label="Descrição" rows="2" />
                    <x-form-icon-picker :icons="$icons" />

                    <div class="rounded-3 border bg-body-tertiary p-3 vstack gap-3">
                        <div>
                            <span class="form-label fw-medium small d-block mb-1"><i class="bi bi-unlock me-1" aria-hidden="true"></i> Como este título é desbloqueado</span>
                            <div class="vstack gap-2">
                                @foreach ($unlockTypes as $unlockType)
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="radio" id="unlock-{{ $unlockType->value }}" value="{{ $unlockType->value }}" x-model="form.unlock_type">
                                        <label class="form-check-label" for="unlock-{{ $unlockType->value }}">{{ $unlockType->label() }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-danger small mt-1" x-text="firstError('unlock_type')"></div>
                        </div>

                        <template x-if="form.unlock_type === '{{ \App\Enums\TitleUnlockType::ACHIEVEMENT->value }}'">
                            <x-form-select name="achievement_id" label="Conquista" placeholder="Nenhuma (concedido manualmente)">
                                <template x-for="achievement in extra.form_options?.achievements ?? []" :key="achievement.id">
                                    <option x-bind:value="achievement.id" x-text="achievement.label"></option>
                                </template>
                            </x-form-select>
                        </template>

                        <template x-if="form.unlock_type === '{{ \App\Enums\TitleUnlockType::CHALLENGE->value }}'">
                            <x-form-select name="challenge_id" label="Desafio" placeholder="Selecione o desafio...">
                                <template x-for="challenge in extra.form_options?.challenges ?? []" :key="challenge.id">
                                    <option x-bind:value="challenge.id" x-text="challenge.label"></option>
                                </template>
                            </x-form-select>
                        </template>

                        <template x-if="form.unlock_type === '{{ \App\Enums\TitleUnlockType::CHALLENGES_COUNT->value }}'">
                            <x-form-input name="challenges_required" label="Quantidade de desafios concluídos" type="number" min="1" placeholder="Ex.: 5" hint="Conta todos os desafios que o usuário já concluiu, de qualquer tipo." />
                        </template>

                        <div x-show="form.unlock_type !== '{{ \App\Enums\TitleUnlockType::ACHIEVEMENT->value }}'" class="form-text mt-0">Quem já cumpriu essa regra recebe o título ao salvar.</div>
                    </div>

                    <template x-if="editingRow"><x-form-switch name="active" label="Ativo" /></template>

                    <x-form-actions create-label="Criar título" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
