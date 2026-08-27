<?php

namespace App\Livewire\Admin\Categories;

use App\Exceptions\DeletionBlockedException;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\TaskCategory;
use App\Repositories\TaskCategoryRepository;
use App\Services\Admin\CategoryService;
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
        $category = app(TaskCategoryRepository::class)->findOrFail($categoryId);

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
        $category = app(TaskCategoryRepository::class)->findOrFail($categoryId);

        $this->authorize('delete', $category);

        $name = $category->name;

        try {
            app(CategoryService::class)->delete($category);
        } catch (DeletionBlockedException $e) {
            $this->addError('delete', $e->getMessage());
            $this->toastError('Não foi possível excluir', $e->getMessage());
            $this->flushToasts();

            return;
        }

        $this->toastSuccess('Categoria excluída', "\"{$name}\" foi excluída.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.categories.index', [
            'categories' => app(TaskCategoryRepository::class)->paginate($this->perPage),
        ]);
    }
}
