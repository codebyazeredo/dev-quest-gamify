<?php

namespace App\Livewire\Admin\Titles;

use App\Enums\TitleUnlockType;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\ValidatesTitleUnlock;
use App\Models\Title;
use App\Repositories\AchievementRepository;
use App\Repositories\ChallengeRepository;
use App\Repositories\TitleRepository;
use App\Services\Admin\TitleService;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;
    use ValidatesTitleUnlock;

    public Title $title;

    public string $name = '';

    public string $description = '';

    public string $icon = '';

    public string $unlock_type = 'achievement';

    public ?int $achievement_id = null;

    public ?int $challenge_id = null;

    public ?int $challenges_required = null;

    public bool $active = true;

    public function mount(int $titleId): void
    {
        $this->title = app(TitleRepository::class)->findOrFail($titleId);

        $this->authorize('update', $this->title);

        $this->name = $this->title->name;
        $this->description = (string) $this->title->description;
        $this->icon = (string) $this->title->icon;
        $this->unlock_type = $this->title->unlock_type->value;
        $this->achievement_id = $this->title->achievement_id;
        $this->challenge_id = $this->title->challenge_id;
        $this->challenges_required = $this->title->challenges_required;
        $this->active = $this->title->active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:titles,name,'.$this->title->id],
            'icon' => ['nullable', 'string', 'max:10'],
            ...$this->unlockRules(),
        ];
    }

    protected function messages(): array
    {
        return $this->unlockMessages();
    }

    public function save(): void
    {
        $this->authorize('update', $this->title);

        $validated = $this->validate();

        $title = app(TitleService::class)->update($this->title, [
            ...$validated,
            'active' => $this->active,
        ]);

        $this->toastSuccess('Título atualizado', "\"{$title->name}\" foi atualizado.");
        $this->flushToasts();

        $this->dispatch('title-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.titles.edit', [
            'achievements' => app(AchievementRepository::class)->all(),
            'challenges' => app(ChallengeRepository::class)->all(),
            'unlockTypes' => TitleUnlockType::cases(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
