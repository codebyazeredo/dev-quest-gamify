<?php

namespace App\Livewire\Admin\Titles;

use App\Enums\TitleUnlockType;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\ValidatesTitleUnlock;
use App\Models\Title;
use App\Repositories\AchievementRepository;
use App\Repositories\ChallengeRepository;
use App\Services\Admin\TitleService;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;
    use ValidatesTitleUnlock;

    public string $name = '';

    public string $description = '';

    public string $icon = 'medal';

    public string $unlock_type = 'achievement';

    public ?int $achievement_id = null;

    public ?int $challenge_id = null;

    public ?int $challenges_required = null;

    public function mount(): void
    {
        $this->authorize('create', Title::class);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:titles,name'],
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
        $this->authorize('create', Title::class);

        $validated = $this->validate();

        $title = app(TitleService::class)->create($validated);

        $this->toastSuccess('Título criado', "\"{$title->name}\" foi criado.");
        $this->flushToasts();

        $this->dispatch('title-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.titles.create', [
            'achievements' => app(AchievementRepository::class)->all(),
            'challenges' => app(ChallengeRepository::class)->all(),
            'unlockTypes' => TitleUnlockType::cases(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
