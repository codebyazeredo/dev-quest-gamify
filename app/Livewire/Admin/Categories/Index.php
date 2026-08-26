<?php

namespace App\Livewire\Admin\Categories;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\TaskCategory;
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

    public ?int $editingCategoryId = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', TaskCategory::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $categoryId): void
    {
        $category = TaskCategory::findOrFail($categoryId);

        $this->authorize('update', $category);

        $this->editingCategoryId = $categoryId;
    }

    #[On('close-modal')]
    #[On('category-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingCategoryId = null;
    }

    public function delete(int $categoryId): void
    {
        $category = TaskCategory::findOrFail($categoryId);

        $this->authorize('delete', $category);

        if ($category->tasks()->exists()) {
            $this->addError('delete', 'Não é possível excluir uma categoria que ainda possui tarefas.');

            return;
        }

        $category->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.categories.index', [
            'categories' => TaskCategory::orderBy('name')->paginate(15),
        ]);
    }
}
