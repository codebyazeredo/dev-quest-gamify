<?php

namespace App\Livewire\Gamification;

use App\Models\Title;
use App\Services\TitleService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Titles extends Component
{
    public function selectTitle(int $titleId): void
    {
        $title = Title::findOrFail($titleId);

        $this->authorize('select', $title);

        app(TitleService::class)->select(auth()->user(), $title);
    }

    public function clearTitle(): void
    {
        app(TitleService::class)->clear(auth()->user());
    }

    public function render(): View
    {
        $user = auth()->user();

        // Admins are the game's GM — every title is already available to use.
        $unlockedTitles = $user->isAdmin()
            ? Title::orderBy('name')->get()
            : $user->unlockedTitles()->with('title')->get()->pluck('title');

        return view('livewire.gamification.titles', [
            'unlockedTitles' => $unlockedTitles,
            'selectedTitleId' => $user->selected_title_id,
        ]);
    }
}
