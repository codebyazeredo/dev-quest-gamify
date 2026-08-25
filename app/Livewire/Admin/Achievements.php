<?php

namespace App\Livewire\Admin;

use App\Enums\AchievementConditionType;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Achievements extends Component
{
    public string $name = '';

    public string $description = '';

    public string $icon = 'trophy';

    public int $condition_type = 1;

    public int $condition_value = 1;

    public int $xp_reward = 10;

    public ?int $editingId = null;

    public string $editingName = '';

    public string $editingDescription = '';

    public string $editingIcon = 'trophy';

    public int $editingConditionType = 1;

    public int $editingConditionValue = 1;

    public int $editingXpReward = 10;

    public bool $editingActive = true;

    public function mount(): void
    {
        Gate::authorize('accessAdminPanel', User::class);
    }

    public function create(): void
    {
        $this->authorize('create', Achievement::class);

        $this->validate([
            'name' => ['required', 'string', 'max:60', 'unique:achievements,name'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:10'],
            'condition_type' => ['required', 'integer'],
            'condition_value' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['required', 'integer', 'min:0'],
        ]);

        Achievement::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'icon' => $this->icon,
            'condition_type' => AchievementConditionType::from($this->condition_type),
            'condition_value' => $this->condition_value,
            'xp_reward' => $this->xp_reward,
        ]);

        $this->reset('name', 'description', 'icon', 'condition_type', 'condition_value', 'xp_reward');
        $this->icon = 'trophy';
        $this->condition_type = 1;
        $this->condition_value = 1;
        $this->xp_reward = 10;
    }

    public function edit(int $achievementId): void
    {
        $achievement = Achievement::findOrFail($achievementId);

        $this->authorize('update', $achievement);

        $this->editingId = $achievement->id;
        $this->editingName = $achievement->name;
        $this->editingDescription = (string) $achievement->description;
        $this->editingIcon = (string) $achievement->icon;
        $this->editingConditionType = $achievement->condition_type->value;
        $this->editingConditionValue = $achievement->condition_value;
        $this->editingXpReward = $achievement->xp_reward;
        $this->editingActive = $achievement->active;
    }

    public function update(): void
    {
        $achievement = Achievement::findOrFail($this->editingId);

        $this->authorize('update', $achievement);

        $this->validate([
            'editingName' => ['required', 'string', 'max:60', 'unique:achievements,name,'.$achievement->id],
            'editingDescription' => ['nullable', 'string'],
            'editingIcon' => ['nullable', 'string', 'max:10'],
            'editingConditionType' => ['required', 'integer'],
            'editingConditionValue' => ['required', 'integer', 'min:1'],
            'editingXpReward' => ['required', 'integer', 'min:0'],
        ]);

        $achievement->update([
            'name' => $this->editingName,
            'slug' => Str::slug($this->editingName),
            'description' => $this->editingDescription,
            'icon' => $this->editingIcon,
            'condition_type' => AchievementConditionType::from($this->editingConditionType),
            'condition_value' => $this->editingConditionValue,
            'xp_reward' => $this->editingXpReward,
            'active' => $this->editingActive,
        ]);

        $this->editingId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function delete(int $achievementId): void
    {
        $achievement = Achievement::findOrFail($achievementId);

        $this->authorize('delete', $achievement);

        if ($achievement->userAchievements()->exists()) {
            $this->addError('delete', 'Cannot delete an achievement that users have already unlocked.');

            return;
        }

        $achievement->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.achievements', [
            'achievements' => Achievement::orderBy('name')->get(),
            'conditionTypes' => AchievementConditionType::cases(),
        ]);
    }
}
