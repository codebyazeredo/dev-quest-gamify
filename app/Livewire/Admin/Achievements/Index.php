<?php

namespace App\Livewire\Admin\Achievements;

use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\Achievement;
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

    public ?int $editingAchievementId = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', Achievement::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $achievementId): void
    {
        $achievement = Achievement::findOrFail($achievementId);

        $this->authorize('update', $achievement);

        $this->editingAchievementId = $achievementId;
    }

    #[On('close-modal')]
    #[On('achievement-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingAchievementId = null;
    }

    public function delete(int $achievementId): void
    {
        $achievement = Achievement::findOrFail($achievementId);

        $this->authorize('delete', $achievement);

        if ($achievement->userAchievements()->exists()) {
            $this->addError('delete', 'Não é possível excluir uma conquista que usuários já desbloquearam.');
            $this->toastError('Não foi possível excluir', 'Esta conquista já foi desbloqueada por usuários.');
            $this->flushToasts();

            return;
        }

        $name = $achievement->name;
        $achievement->delete();

        $this->toastSuccess('Conquista excluída', "\"{$name}\" foi excluída.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.achievements.index', [
            'achievements' => Achievement::orderBy('name')->paginate($this->perPage),
        ]);
    }
}
