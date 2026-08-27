<?php

namespace App\Livewire\Admin\Achievements;

use App\Enums\AchievementConditionType;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Achievement;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;

    public string $name = '';

    public string $description = '';

    public string $icon = 'trophy';

    public int $condition_type = 1;

    public int $condition_value = 1;

    public int $xp_reward = 10;

    public function mount(): void
    {
        $this->authorize('create', Achievement::class);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:achievements,name'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:10'],
            'condition_type' => ['required', 'integer'],
            'condition_value' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['required', 'integer', 'min:0'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Achievement::class);

        $validated = $this->validate();

        Achievement::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'condition_type' => AchievementConditionType::from($validated['condition_type']),
            'condition_value' => $validated['condition_value'],
            'xp_reward' => $validated['xp_reward'],
        ]);

        $this->toastSuccess('Conquista criada', "\"{$validated['name']}\" foi criada.");
        $this->flushToasts();

        $this->dispatch('achievement-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.achievements.create', [
            'conditionTypes' => AchievementConditionType::cases(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
