<?php

namespace App\Livewire\Admin\People;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\Person;
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

            return;
        }

        $person->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.people.index', [
            'people' => Person::orderBy('nome')->paginate(15),
        ]);
    }
}
