<div class="rounded-3 border bg-body-tertiary p-3 vstack gap-3">
    <div>
        <span class="form-label fw-medium small d-block mb-1"><i class="bi bi-unlock me-1" aria-hidden="true"></i> Como este título é desbloqueado</span>
        <div class="vstack gap-2">
            @foreach ($unlockTypes as $unlockOption)
                <div class="form-check mb-0">
                    <input class="form-check-input" type="radio" id="unlock-{{ $unlockOption->value }}" value="{{ $unlockOption->value }}" wire:model.live="unlock_type">
                    <label class="form-check-label" for="unlock-{{ $unlockOption->value }}">{{ $unlockOption->label() }}</label>
                </div>
            @endforeach
        </div>
        @error('unlock_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    @if ($unlock_type === \App\Enums\TitleUnlockType::ACHIEVEMENT->value)
        <x-select name="achievement_id" label="Conquista" wire:model="achievement_id" placeholder="Nenhuma (concedido manualmente)">
            @foreach ($achievements as $achievement)
                <option value="{{ $achievement->id }}">{{ $achievement->name }}</option>
            @endforeach
        </x-select>
    @elseif ($unlock_type === \App\Enums\TitleUnlockType::CHALLENGE->value)
        <x-select name="challenge_id" label="Desafio" wire:model="challenge_id" placeholder="Selecione o desafio...">
            @foreach ($challenges as $challenge)
                <option value="{{ $challenge->id }}">{{ $challenge->name }} ({{ $challenge->statusLabel() }})</option>
            @endforeach
        </x-select>
    @else
        <div>
            <x-input name="challenges_required" label="Quantidade de desafios concluídos" type="number" min="1" wire:model="challenges_required" placeholder="Ex.: 5" />
            <div class="form-text">Conta todos os desafios que o usuário já concluiu, de qualquer tipo.</div>
        </div>
    @endif

    @if ($unlock_type !== \App\Enums\TitleUnlockType::ACHIEVEMENT->value)
        <div class="form-text mt-0">Quem já cumpriu essa regra recebe o título ao salvar.</div>
    @endif
</div>
