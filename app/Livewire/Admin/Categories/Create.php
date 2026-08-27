<?php

namespace App\Livewire\Admin\Categories;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\TaskCategory;
use App\Services\Admin\CategoryService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;

    public string $name = '';

    public int $base_points = 10;

    public string $color = '#e2e5ea';

    public string $text_color = '#1b2733';

    public function mount(): void
    {
        $this->authorize('create', TaskCategory::class);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:task_categories,name'],
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
        $this->authorize('create', TaskCategory::class);

        $validated = $this->validate();

        $category = app(CategoryService::class)->create($validated);

        $this->toastSuccess('Categoria criada', "\"{$category->name}\" foi criada.");
        $this->flushToasts();

        $this->dispatch('category-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.categories.create', [
            'colorPairs' => TaskCategory::COLOR_PRESETS,
        ]);
    }
}
