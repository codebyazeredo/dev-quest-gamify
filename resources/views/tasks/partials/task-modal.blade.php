<div
    x-data="taskModal(@js($taskForms), @js($categories->pluck('name', 'id')))"
    x-on:open-task-form.window="openTask($event.detail)"
    x-on:close-modal="close"
>
    <template x-if="isOpen">
        <x-modal title="Tarefa" size="lg">
            <form x-on:submit.prevent="save" class="vstack gap-3">
                <x-form-input name="title" label="Título" placeholder="O que precisa ser feito?" required autofocus />
                <x-form-textarea name="description" label="Descrição" rows="4" placeholder="Contexto, critérios de aceite, links úteis..." />

                <section class="form-section">
                    <div class="form-section-title"><i class="bi bi-sliders" aria-hidden="true"></i> Classificação</div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <x-form-select name="category_id" label="Categoria" placeholder="Selecionar..." x-bind:disabled="isEdit && isLocked">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-form-select>
                        </div>

                        <div class="col-md-4">
                            <x-form-select name="priority_id" label="Prioridade" placeholder="Selecionar..." x-bind:disabled="isEdit && isLocked">
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                @endforeach
                            </x-form-select>
                        </div>

                        <div class="col-md-4"><x-form-input name="due_at" label="Prazo" type="datetime-local" /></div>
                    </div>

                    <p x-show="isEdit && isLocked" x-cloak class="small text-body-secondary mt-3 mb-0">
                        <i class="bi bi-lock" aria-hidden="true"></i>
                        Categoria e prioridade estão bloqueadas porque esta tarefa já foi concluída.
                    </p>

                    @if (($assignableUsers ?? collect())->isNotEmpty())
                        <template x-if="! isEdit">
                            <div class="mt-3">
                                <label class="form-label fw-medium small mb-1">Atribuir a</label>
                                <x-user-picker :users="$assignableUsers" x-model="form.assigned_to" />
                            </div>
                        </template>
                    @endif
                </section>

                <section class="form-section" x-effect="syncBranch()">
                    <div class="form-section-title"><i class="bi bi-git" aria-hidden="true"></i> Git</div>

                    <div class="vstack gap-3">
                        <div>
                            <label for="branch_name" class="form-label fw-medium small mb-1">Branch</label>
                            <div class="input-group">
                                <input
                                    id="branch_name"
                                    type="text"
                                    x-model="form.branch_name"
                                    x-on:input="isBranchAuto = false"
                                    x-bind:class="{ 'is-invalid': firstError('branch_name') }"
                                    class="form-control"
                                    placeholder="Gerada a partir do tipo e do título"
                                    autocomplete="off"
                                    spellcheck="false"
                                >
                                <button type="button" class="btn btn-outline-secondary" x-on:click="regenerateBranch" data-bs-toggle="tooltip" data-bs-title="Gerar a partir do tipo e do título" aria-label="Gerar nome da branch">
                                    <i class="bi bi-magic" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" x-bind:data-copy="form.branch_name" data-copy-label="Branch copiada" x-bind:disabled="! form.branch_name" data-bs-toggle="tooltip" data-bs-title="Copiar nome da branch" aria-label="Copiar nome da branch">
                                    <i class="bi bi-clipboard" aria-hidden="true"></i>
                                </button>
                                <div class="invalid-feedback" x-text="firstError('branch_name')"></div>
                            </div>
                            <div class="form-text">Sugerida pelo tipo e título da tarefa. Edite se precisar; use <i class="bi bi-magic" aria-hidden="true"></i> para voltar à sugestão.</div>
                        </div>

                        <div>
                            <label for="pull_request_url" class="form-label fw-medium small mb-1">Link do pull request</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-link-45deg" aria-hidden="true"></i></span>
                                <input
                                    id="pull_request_url"
                                    type="url"
                                    x-model="form.pull_request_url"
                                    x-bind:class="{ 'is-invalid': firstError('pull_request_url') }"
                                    class="form-control"
                                    placeholder="https://github.com/org/repo/pull/1"
                                    autocomplete="off"
                                >
                                <button type="button" class="btn btn-outline-secondary" x-bind:data-copy="form.pull_request_url" data-copy-label="Link copiado" x-bind:disabled="! form.pull_request_url" data-bs-toggle="tooltip" data-bs-title="Copiar link" aria-label="Copiar link do pull request">
                                    <i class="bi bi-clipboard" aria-hidden="true"></i>
                                </button>
                                <a x-show="isPullRequestLink()" x-cloak x-bind:href="form.pull_request_url" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-title="Abrir pull request" aria-label="Abrir pull request">
                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                </a>
                                <div class="invalid-feedback" x-text="firstError('pull_request_url')"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="d-flex justify-content-end gap-2">
                    <x-button variant="secondary" x-on:click="close">Cancelar</x-button>
                    <x-button type="submit" x-bind:disabled="isSaving"><i class="bi bi-check-lg" aria-hidden="true"></i> <span x-text="isEdit ? 'Salvar' : 'Criar tarefa'"></span></x-button>
                </div>
            </form>
        </x-modal>
    </template>
</div>
