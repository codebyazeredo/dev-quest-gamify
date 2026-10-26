<?php

namespace App\Livewire\Admin\Challenges;

use App\Exceptions\DeletionBlockedException;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\Challenge;
use App\Repositories\ChallengeRepository;
use App\Services\Admin\ChallengeService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
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

    #[Url(as: 'busca')]
    public string $search = '';

    #[Url(as: 'situacao')]
    public string $status = '';

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
        $challenge = app(ChallengeRepository::class)->findOrFail($challengeId);

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
        $challenge = app(ChallengeRepository::class)->findOrFail($challengeId);

        $this->authorize('delete', $challenge);

        $name = $challenge->name;

        try {
            app(ChallengeService::class)->delete($challenge);
        } catch (DeletionBlockedException $e) {
            $this->addError('delete', $e->getMessage());
            $this->toastError('Não foi possível excluir', 'Usuários já fizeram progresso neste desafio.');
            $this->flushToasts();

            return;
        }

        $this->toastSuccess('Desafio excluído', "\"{$name}\" foi excluído.");
        $this->flushToasts();
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function toggleActive(int $challengeId): void
    {
        $challenge = app(ChallengeRepository::class)->findOrFail($challengeId);

        $this->authorize('update', $challenge);

        $challenge = app(ChallengeService::class)->toggleActive($challenge);

        $this->toastSuccess($challenge->active ? 'Desafio ativado' : 'Desafio desativado', "\"{$challenge->name}\" foi atualizado.");
        $this->flushToasts();
    }

    public function duplicate(int $challengeId): void
    {
        $this->authorize('create', Challenge::class);

        $source = app(ChallengeRepository::class)->findOrFail($challengeId);

        $copy = app(ChallengeService::class)->duplicate($source);

        $this->toastSuccess('Desafio duplicado', "\"{$copy->name}\" foi criado como inativo para o próximo período.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.challenges.index', [
            'challenges' => app(ChallengeRepository::class)->paginateFiltered($this->perPage, trim($this->search), $this->status),
        ]);
    }
}
