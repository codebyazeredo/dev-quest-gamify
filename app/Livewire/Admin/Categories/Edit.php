<?php

namespace App\Livewire\Admin\Categories;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\TaskCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public TaskCategory $category;

    public string $name = '';

    public int $base_points = 10;

    public string $color = '#e2e5ea';

    public string $text_color = '#1b2733';

    public function mount(int $categoryId): void
    {
        $this->category = TaskCategory::findOrFail($categoryId);

        $this->authorize('update', $this->category);

        $this->name = $this->category->name;
        $this->base_points = $this->category->base_points;
        $this->color = $this->category->color;
        $this->text_color = $this->category->text_color;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:task_categories,name,'.$this->category->id],
            'base_points' => ['required', 'integer', 'min:0'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'text_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    public function selectColor(string $bg, string $text): void
    {
        $this->color = $bg;
        $this->text_color = $text;
    }

    public function save(): void
    {
        $this->authorize('update', $this->category);

        $validated = $this->validate();

        $this->category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'base_points' => $validated['base_points'],
            'color' => $validated['color'],
            'text_color' => $validated['text_color'],
        ]);

        $this->toastSuccess('Categoria atualizada', "\"{$validated['name']}\" foi atualizada.");
        $this->flushToasts();

        $this->dispatch('category-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.categories.edit', [
            'colorPairs' => TaskCategory::COLOR_PRESETS,
        ]);
    }
}
