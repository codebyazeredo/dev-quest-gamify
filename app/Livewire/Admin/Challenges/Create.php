<?php

namespace App\Livewire\Admin\Challenges;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
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

    /**
     * @return array<string, mixed>
     */
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

        Challenge::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'type' => ChallengeType::from($validated['type']),
            'target' => $validated['target'],
            'xp_reward' => $validated['xp_reward'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
        ]);

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
