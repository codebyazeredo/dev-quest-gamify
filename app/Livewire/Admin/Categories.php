<?php

namespace App\Livewire\Admin;

use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Categories extends Component
{
    public string $name = '';

    public int $base_points = 10;

    public ?int $editingId = null;

    public string $editingName = '';

    public int $editingBasePoints = 10;

    public function mount(): void
    {
        Gate::authorize('accessAdminPanel', User::class);
    }

    public function create(): void
    {
        $this->authorize('create', TaskCategory::class);

        $this->validate([
            'name' => ['required', 'string', 'max:60', 'unique:task_categories,name'],
            'base_points' => ['required', 'integer', 'min:0'],
        ]);

        TaskCategory::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'base_points' => $this->base_points,
        ]);

        $this->reset('name', 'base_points');
        $this->base_points = 10;
    }

    public function edit(int $categoryId): void
    {
        $category = TaskCategory::findOrFail($categoryId);

        $this->authorize('update', $category);

        $this->editingId = $category->id;
        $this->editingName = $category->name;
        $this->editingBasePoints = $category->base_points;
    }

    public function update(): void
    {
        $category = TaskCategory::findOrFail($this->editingId);

        $this->authorize('update', $category);

        $this->validate([
            'editingName' => ['required', 'string', 'max:60', 'unique:task_categories,name,'.$category->id],
            'editingBasePoints' => ['required', 'integer', 'min:0'],
        ]);

        $category->update([
            'name' => $this->editingName,
            'slug' => Str::slug($this->editingName),
            'base_points' => $this->editingBasePoints,
        ]);

        $this->editingId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function delete(int $categoryId): void
    {
        $category = TaskCategory::findOrFail($categoryId);

        $this->authorize('delete', $category);

        if ($category->tasks()->exists()) {
            $this->addError('delete', 'Cannot delete a category that still has tasks.');

            return;
        }

        $category->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.categories', [
            'categories' => TaskCategory::orderBy('name')->get(),
        ]);
    }
}
