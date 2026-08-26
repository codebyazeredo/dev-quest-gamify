<?php

namespace App\Livewire\Admin\Categories;

use App\Models\TaskCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public TaskCategory $category;

    public string $name = '';

    public int $base_points = 10;

    public function mount(int $categoryId): void
    {
        $this->category = TaskCategory::findOrFail($categoryId);

        $this->authorize('update', $this->category);

        $this->name = $this->category->name;
        $this->base_points = $this->category->base_points;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:task_categories,name,'.$this->category->id],
            'base_points' => ['required', 'integer', 'min:0'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->category);

        $validated = $this->validate();

        $this->category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'base_points' => $validated['base_points'],
        ]);

        $this->dispatch('category-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.categories.edit');
    }
}
