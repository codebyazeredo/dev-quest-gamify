<?php

namespace App\Livewire\Admin;

use App\Enums\ChallengeType;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\Challenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Challenges extends Component
{
    use RequiresAdminAccess;

    public string $name = '';

    public string $description = '';

    public int $type = 1;

    public int $target = 1;

    public int $xp_reward = 10;

    public string $starts_at = '';

    public string $ends_at = '';

    public ?int $editingId = null;

    public string $editingName = '';

    public string $editingDescription = '';

    public int $editingType = 1;

    public int $editingTarget = 1;

    public int $editingXpReward = 10;

    public string $editingStartsAt = '';

    public string $editingEndsAt = '';

    public bool $editingActive = true;

    public function mount(): void
    {
        $this->ensureAdminAccess();

        $this->starts_at = now()->startOfWeek()->format('Y-m-d\TH:i');
        $this->ends_at = now()->endOfWeek()->format('Y-m-d\TH:i');
    }

    public function create(): void
    {
        $this->authorize('create', Challenge::class);

        $this->validate([
            'name' => ['required', 'string', 'max:60', 'unique:challenges,name'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'integer'],
            'target' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['required', 'integer', 'min:0'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);

        Challenge::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'type' => ChallengeType::from($this->type),
            'target' => $this->target,
            'xp_reward' => $this->xp_reward,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ]);

        $this->reset('name', 'description', 'type', 'target', 'xp_reward');
        $this->type = 1;
        $this->target = 1;
        $this->xp_reward = 10;
    }

    public function edit(int $challengeId): void
    {
        $challenge = Challenge::findOrFail($challengeId);

        $this->authorize('update', $challenge);

        $this->editingId = $challenge->id;
        $this->editingName = $challenge->name;
        $this->editingDescription = (string) $challenge->description;
        $this->editingType = $challenge->type->value;
        $this->editingTarget = $challenge->target;
        $this->editingXpReward = $challenge->xp_reward;
        $this->editingStartsAt = $challenge->starts_at->format('Y-m-d\TH:i');
        $this->editingEndsAt = $challenge->ends_at->format('Y-m-d\TH:i');
        $this->editingActive = $challenge->active;
    }

    public function update(): void
    {
        $challenge = Challenge::findOrFail($this->editingId);

        $this->authorize('update', $challenge);

        $this->validate([
            'editingName' => ['required', 'string', 'max:60', 'unique:challenges,name,'.$challenge->id],
            'editingDescription' => ['nullable', 'string'],
            'editingType' => ['required', 'integer'],
            'editingTarget' => ['required', 'integer', 'min:1'],
            'editingXpReward' => ['required', 'integer', 'min:0'],
            'editingStartsAt' => ['required', 'date'],
            'editingEndsAt' => ['required', 'date', 'after:editingStartsAt'],
        ]);

        $challenge->update([
            'name' => $this->editingName,
            'slug' => Str::slug($this->editingName),
            'description' => $this->editingDescription,
            'type' => ChallengeType::from($this->editingType),
            'target' => $this->editingTarget,
            'xp_reward' => $this->editingXpReward,
            'starts_at' => $this->editingStartsAt,
            'ends_at' => $this->editingEndsAt,
            'active' => $this->editingActive,
        ]);

        $this->editingId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function delete(int $challengeId): void
    {
        $challenge = Challenge::findOrFail($challengeId);

        $this->authorize('delete', $challenge);

        if ($challenge->userChallenges()->exists()) {
            $this->addError('delete', 'Cannot delete a challenge that users have already made progress on.');

            return;
        }

        $challenge->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.challenges', [
            'challenges' => Challenge::orderByDesc('starts_at')->get(),
            'challengeTypes' => ChallengeType::cases(),
        ]);
    }
}
