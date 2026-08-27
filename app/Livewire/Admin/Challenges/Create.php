<?php

namespace App\Livewire\Admin\Challenges;

use App\Enums\ChallengeType;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Challenge;
use App\Services\Admin\ChallengeService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;

    public string $name = '';

    public string $description = '';

    public int $type = 1;

    public int $target = 1;

    public int $xp_reward = 10;

    public string $starts_at = '';

    public string $ends_at = '';

    public function mount(): void
    {
        $this->authorize('create', Challenge::class);

        $this->starts_at = now()->startOfWeek()->format('Y-m-d\TH:i');
        $this->ends_at = now()->endOfWeek()->format('Y-m-d\TH:i');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:challenges,name'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'integer'],
            'target' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['required', 'integer', 'min:0'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Challenge::class);

        $validated = $this->validate();

        $challenge = app(ChallengeService::class)->create($validated);

        $this->toastSuccess('Desafio criado', "\"{$challenge->name}\" foi criado.");
        $this->flushToasts();

        $this->dispatch('challenge-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.challenges.create', [
            'challengeTypes' => ChallengeType::cases(),
        ]);
    }
}
