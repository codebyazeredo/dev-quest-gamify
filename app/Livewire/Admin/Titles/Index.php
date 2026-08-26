<?php

namespace App\Livewire\Admin\Titles;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\Title;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use RequiresAdminAccess;
    use WithPagination;

    public bool $showCreateModal = false;

    public ?int $editingTitleId = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', Title::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $titleId): void
    {
        $title = Title::findOrFail($titleId);

        $this->authorize('update', $title);

        $this->editingTitleId = $titleId;
    }

    #[On('close-modal')]
    #[On('title-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingTitleId = null;
    }

    public function delete(int $titleId): void
    {
        $title = Title::findOrFail($titleId);

        $this->authorize('delete', $title);

        if ($title->userTitles()->exists()) {
            $this->addError('delete', 'Não é possível excluir um título que usuários já desbloquearam.');

            return;
        }

        $title->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.titles.index', [
            'titles' => Title::with('achievement')->orderBy('name')->paginate(15),
        ]);
    }
}
