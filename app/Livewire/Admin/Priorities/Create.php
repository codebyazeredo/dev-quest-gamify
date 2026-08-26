<?php

namespace App\Livewire\Admin\Priorities;

use App\Models\TaskPriority;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $multiplier = '1.00';

    public function mount(): void
    {
        $this->authorize('create', TaskPriority::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:task_priorities,name'],
            'multiplier' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', TaskPriority::class);

        $validated = $this->validate();

        TaskPriority::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'multiplier' => $validated['multiplier'],
        ]);

        $this->dispatch('priority-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.priorities.create');
    }
}
