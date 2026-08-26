<?php

namespace App\Livewire\Admin\Priorities;

use App\Models\TaskPriority;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public TaskPriority $priority;

    public string $name = '';

    public string $multiplier = '1.00';

    public function mount(int $priorityId): void
    {
        $this->priority = TaskPriority::findOrFail($priorityId);

        $this->authorize('update', $this->priority);

        $this->name = $this->priority->name;
        $this->multiplier = (string) $this->priority->multiplier;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'unique:task_priorities,name,'.$this->priority->id],
            'multiplier' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->priority);

        $validated = $this->validate();

        $this->priority->update([
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
        return view('livewire.admin.priorities.edit');
    }
}
