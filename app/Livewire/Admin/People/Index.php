<?php

namespace App\Livewire\Admin\People;

use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\Person;
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

    public ?int $editingPersonId = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', Person::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $personId): void
    {
        $person = Person::findOrFail($personId);

        $this->authorize('update', $person);

        $this->editingPersonId = $personId;
    }

    #[On('close-modal')]
    #[On('person-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingPersonId = null;
    }

    public function delete(int $personId): void
    {
        $person = Person::findOrFail($personId);

        $this->authorize('delete', $person);

        if ($person->user()->exists()) {
            $this->addError('delete', 'Não é possível excluir uma pessoa que já possui um usuário vinculado.');
            $this->toastError('Não foi possível excluir', 'Esta pessoa já possui um usuário vinculado.');
            $this->flushToasts();

            return;
        }

        $nome = $person->nome;
        $person->delete();

        $this->toastSuccess('Pessoa excluída', "\"{$nome}\" foi excluída.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.people.index', [
            'people' => Person::orderBy('nome')->paginate($this->perPage),
        ]);
    }
}
