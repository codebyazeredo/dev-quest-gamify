{{-- Modal de criar/editar tarefa. Espera $taskForms (dados editáveis por id), $categories e $priorities. --}}
<div
    x-data="taskModal(@js($taskForms))"
    x-on:open-task-form.window="openTask($event.detail)"
    x-on:close-modal="close"
>
    <template x-if="isOpen">
        <x-modal title="Tarefa">
            <form x-on:submit.prevent="save" class="vstack gap-3">
                <x-form-input name="title" label="Título" required autofocus />
                <x-form-textarea name="description" label="Descrição" />

                <div class="row g-3">
                    <div class="col-sm-6">
                        <x-form-select name="category_id" label="Categoria" placeholder="Selecionar..." x-bind:disabled="isEdit && isLocked">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-form-select>
                    </div>

                    <div class="col-sm-6">
                        <x-form-select name="priority_id" label="Prioridade" placeholder="Selecionar..." x-bind:disabled="isEdit && isLocked">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                            @endforeach
                        </x-form-select>
                    </div>
                </div>

                <p x-show="isEdit && isLocked" x-cloak class="small text-body-secondary mb-0">
                    <i class="bi bi-lock" aria-hidden="true"></i>
                    Categoria e prioridade estão bloqueadas porque esta tarefa já foi concluída.
                </p>

                @if (($assignableUsers ?? collect())->isNotEmpty())
                    <template x-if="! isEdit">
                        <div>
                            <label class="form-label fw-medium small mb-1">Atribuir a</label>
                            <x-user-picker :users="$assignableUsers" x-model="form.assigned_to" />
                        </div>
                    </template>
                @endif

                <x-form-input name="due_at" label="Prazo" type="datetime-local" />

                <div class="d-flex justify-content-end gap-2">
                    <x-button variant="secondary" x-on:click="close">Cancelar</x-button>
                    <x-button type="submit" x-bind:disabled="isSaving" x-text="isEdit ? 'Salvar' : 'Criar tarefa'"></x-button>
                </div>
            </form>
        </x-modal>
    </template>
</div>
