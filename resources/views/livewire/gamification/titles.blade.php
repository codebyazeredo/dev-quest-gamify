<div>
    <a href="{{ route('dashboard') }}" class="mb-4 inline-flex items-center gap-1 rounded-lg border border-line px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
        ← Minha conta
    </a>

    <h1 class="mb-6 text-2xl font-bold tracking-tight text-ink">Títulos</h1>

    @if ($unlockedTitles->isEmpty())
        <p class="text-sm text-ink-muted">Você ainda não desbloqueou nenhum título.</p>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($unlockedTitles as $title)
                <div class="flex items-center justify-between rounded-xl border p-4 {{ $selectedTitleId === $title->id ? 'border-primary/40 bg-primary/5' : 'border-line bg-card shadow-sm' }}">
                    <div class="flex items-center gap-2">
                        <span class="text-gold">
                            <x-icon :name="$title->icon" class="h-6 w-6" />
                        </span>
                        <span class="font-semibold text-ink">{{ $title->name }}</span>
                    </div>

                    @if ($selectedTitleId === $title->id)
                        <button type="button" wire:click="clearTitle" class="rounded-lg border border-line px-2 py-1 text-xs font-medium text-ink hover:bg-line/20">
                            Remover
                        </button>
                    @else
                        <button type="button" wire:click="selectTitle({{ $title->id }})" class="rounded-lg bg-primary px-2 py-1 text-xs font-semibold text-white hover:bg-primary-hover">
                            Selecionar
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
