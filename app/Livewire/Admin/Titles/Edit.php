<?php

namespace App\Livewire\Admin\Titles;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\Title;
use App\Repositories\AchievementRepository;
use App\Repositories\TitleRepository;
use App\Services\Admin\TitleService;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public Title $title;

    public string $name = '';

    public string $icon = '';

    public ?int $achievement_id = null;

    public bool $active = true;

    public function mount(int $titleId): void
    {
        $this->title = app(TitleRepository::class)->findOrFail($titleId);

        $this->authorize('update', $this->title);

        $this->name = $this->title->name;
        $this->icon = (string) $this->title->icon;
        $this->achievement_id = $this->title->achievement_id;
        $this->active = $this->title->active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:titles,name,'.$this->title->id],
            'icon' => ['nullable', 'string', 'max:10'],
            'achievement_id' => ['nullable', 'exists:achievements,id'],
        ];
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
            'icons' => FlavorIcons::all(),
        ]);
    }
}
