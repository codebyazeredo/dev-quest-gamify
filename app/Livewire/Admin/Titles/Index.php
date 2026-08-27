<?php

namespace App\Livewire\Admin\Titles;

use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\Title;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use FlushesToasts;
    use RequiresAdminAccess;
    use WithAdjustablePerPage;
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
            $this->toastError('Não foi possível excluir', 'Usuários já desbloquearam este título.');
            $this->flushToasts();

            return;
        }

        $name = $title->name;
        $title->delete();

        $this->toastSuccess('Título excluído', "\"{$name}\" foi excluído.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.titles.index', [
            'titles' => Title::with('achievement')->orderBy('name')->paginate($this->perPage),
        ]);
    }
}
