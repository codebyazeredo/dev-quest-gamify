<?php

namespace App\Livewire\Admin\Titles;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\Achievement;
use App\Models\Title;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
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
        $this->title = Title::findOrFail($titleId);

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

        $this->title->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'],
            'achievement_id' => $validated['achievement_id'],
            'active' => $this->active,
        ]);

        $this->toastSuccess('Título atualizado', "\"{$validated['name']}\" foi atualizado.");
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
            'achievements' => Achievement::orderBy('name')->get(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
