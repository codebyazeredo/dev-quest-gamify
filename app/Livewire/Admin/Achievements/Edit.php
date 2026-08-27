<?php

namespace App\Livewire\Admin\Achievements;

use App\Enums\AchievementConditionType;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Achievement;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public Achievement $achievement;

    public string $name = '';

    public string $description = '';

    public string $icon = 'trophy';

    public int $condition_type = 1;

    public int $condition_value = 1;

    public int $xp_reward = 10;

    public bool $active = true;

    public function mount(int $achievementId): void
    {
        $this->achievement = Achievement::findOrFail($achievementId);

        $this->authorize('update', $this->achievement);

        $this->name = $this->achievement->name;
        $this->description = (string) $this->achievement->description;
        $this->icon = (string) $this->achievement->icon;
        $this->condition_type = $this->achievement->condition_type->value;
        $this->condition_value = $this->achievement->condition_value;
        $this->xp_reward = $this->achievement->xp_reward;
        $this->active = $this->achievement->active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:achievements,name,'.$this->achievement->id],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:10'],
            'condition_type' => ['required', 'integer'],
            'condition_value' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['required', 'integer', 'min:0'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->achievement);

        $validated = $this->validate();

        $this->achievement->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'condition_type' => AchievementConditionType::from($validated['condition_type']),
            'condition_value' => $validated['condition_value'],
            'xp_reward' => $validated['xp_reward'],
            'active' => $this->active,
        ]);

        $this->toastSuccess('Conquista atualizada', "\"{$validated['name']}\" foi atualizada.");
        $this->flushToasts();

        $this->dispatch('achievement-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.achievements.edit', [
            'conditionTypes' => AchievementConditionType::cases(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
