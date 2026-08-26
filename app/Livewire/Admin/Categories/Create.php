<?php

namespace App\Livewire\Admin\Categories;

use App\Models\TaskCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public int $base_points = 10;

    public function mount(): void
    {
        $this->authorize('create', TaskCategory::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:task_categories,name'],
            'base_points' => ['required', 'integer', 'min:0'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', TaskCategory::class);

        $validated = $this->validate();

        TaskCategory::create([
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
        return view('livewire.admin.categories.create');
    }
}
