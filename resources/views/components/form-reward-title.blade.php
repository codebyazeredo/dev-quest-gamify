@props(['icons', 'source' => 'desafio'])

<div class="rounded-3 border bg-body-tertiary p-3">
    <div class="form-check form-switch mb-0">
        <input class="form-check-input" type="checkbox" role="switch" id="award_title" x-model="form.award_title">
        <label class="form-check-label fw-medium" for="award_title">
            <i class="bi bi-patch-check me-1" aria-hidden="true"></i>
            <span x-text="editingRow?.titles?.length ? 'Criar mais um título para este {{ $source }}' : 'Conceder um título ao concluir'"></span>
        </label>
    </div>

    <div x-show="editingRow?.titles?.length" x-cloak class="small text-body-secondary mt-2">
        Títulos já ligados:
        <template x-for="linked in editingRow?.titles ?? []" :key="linked.name">
            <span class="lozenge bg-warning-subtle text-warning-emphasis ms-1" x-text="linked.name"></span>
        </template>
    </div>

    <template x-if="form.award_title">
        <div class="vstack gap-3 mt-3">
            <x-form-input name="reward_title_name" label="Nome do título" placeholder="Ex.: Caçador de Bugs" />
            <x-form-icon-picker name="reward_title_icon" :icons="$icons" label="Ícone do título" />
            <div class="form-text mt-0">O título é criado ao salvar e concedido a quem concluir este {{ $source }}. Usuários que já cumpriram a regra também recebem.</div>
        </div>
    </template>
</div>
