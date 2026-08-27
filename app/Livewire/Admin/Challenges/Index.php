<?php

namespace App\Livewire\Admin\Challenges;

use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\Challenge;
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

    public ?int $editingChallengeId = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', Challenge::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $challengeId): void
    {
        $challenge = Challenge::findOrFail($challengeId);

        $this->authorize('update', $challenge);

        $this->editingChallengeId = $challengeId;
    }

    #[On('close-modal')]
    #[On('challenge-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingChallengeId = null;
    }

    public function delete(int $challengeId): void
    {
        $challenge = Challenge::findOrFail($challengeId);

        $this->authorize('delete', $challenge);

        if ($challenge->userChallenges()->exists()) {
            $this->addError('delete', 'Não é possível excluir um desafio no qual usuários já fizeram progresso.');
            $this->toastError('Não foi possível excluir', 'Usuários já fizeram progresso neste desafio.');
            $this->flushToasts();

            return;
        }

        $name = $challenge->name;
        $challenge->delete();

        $this->toastSuccess('Desafio excluído', "\"{$name}\" foi excluído.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.challenges.index', [
            'challenges' => Challenge::orderByDesc('starts_at')->paginate($this->perPage),
        ]);
    }
}
