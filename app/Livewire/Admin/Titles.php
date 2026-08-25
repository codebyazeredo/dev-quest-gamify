<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\Achievement;
use App\Models\Title;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Titles extends Component
{
    use RequiresAdminAccess;

    public string $name = '';

    public string $icon = 'medal';

    public ?int $achievement_id = null;

    public ?int $editingId = null;

    public string $editingName = '';

    public string $editingIcon = '';

    public ?int $editingAchievementId = null;

    public bool $editingActive = true;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function create(): void
    {
        $this->authorize('create', Title::class);

        $this->validate([
            'name' => ['required', 'string', 'max:60', 'unique:titles,name'],
            'icon' => ['nullable', 'string', 'max:10'],
            'achievement_id' => ['nullable', 'exists:achievements,id'],
        ]);

        Title::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'icon' => $this->icon,
            'achievement_id' => $this->achievement_id,
        ]);

        $this->reset('name', 'icon', 'achievement_id');
        $this->icon = 'medal';
    }

    public function edit(int $titleId): void
    {
        $title = Title::findOrFail($titleId);

        $this->authorize('update', $title);

        $this->editingId = $title->id;
        $this->editingName = $title->name;
        $this->editingIcon = (string) $title->icon;
        $this->editingAchievementId = $title->achievement_id;
        $this->editingActive = $title->active;
    }

    public function update(): void
    {
        $title = Title::findOrFail($this->editingId);

        $this->authorize('update', $title);

        $this->validate([
            'editingName' => ['required', 'string', 'max:60', 'unique:titles,name,'.$title->id],
            'editingIcon' => ['nullable', 'string', 'max:10'],
            'editingAchievementId' => ['nullable', 'exists:achievements,id'],
        ]);

        $title->update([
            'name' => $this->editingName,
            'slug' => Str::slug($this->editingName),
            'icon' => $this->editingIcon,
            'achievement_id' => $this->editingAchievementId,
            'active' => $this->editingActive,
        ]);

        $this->editingId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function delete(int $titleId): void
    {
        $title = Title::findOrFail($titleId);

        $this->authorize('delete', $title);

        if ($title->userTitles()->exists()) {
            $this->addError('delete', 'Cannot delete a title that users have already unlocked.');

            return;
        }

        $title->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.titles', [
            'titles' => Title::with('achievement')->orderBy('name')->get(),
            'achievements' => Achievement::orderBy('name')->get(),
        ]);
    }
}
