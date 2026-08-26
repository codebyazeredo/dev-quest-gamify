<?php

namespace App\Livewire\Admin\Challenges;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public Challenge $challenge;

    public string $name = '';

    public string $description = '';

    public int $type = 1;

    public int $target = 1;

    public int $xp_reward = 10;

    public string $starts_at = '';

    public string $ends_at = '';

    public bool $active = true;

    public function mount(int $challengeId): void
    {
        $this->challenge = Challenge::findOrFail($challengeId);

        $this->authorize('update', $this->challenge);

        $this->name = $this->challenge->name;
        $this->description = (string) $this->challenge->description;
        $this->type = $this->challenge->type->value;
        $this->target = $this->challenge->target;
        $this->xp_reward = $this->challenge->xp_reward;
        $this->starts_at = $this->challenge->starts_at->format('Y-m-d\TH:i');
        $this->ends_at = $this->challenge->ends_at->format('Y-m-d\TH:i');
        $this->active = $this->challenge->active;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:challenges,name,'.$this->challenge->id],
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
        $this->authorize('update', $this->challenge);

        $validated = $this->validate();

        $this->challenge->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'type' => ChallengeType::from($validated['type']),
            'target' => $validated['target'],
            'xp_reward' => $validated['xp_reward'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'active' => $this->active,
        ]);

        $this->dispatch('challenge-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.challenges.edit', [
            'challengeTypes' => ChallengeType::cases(),
        ]);
    }
}
