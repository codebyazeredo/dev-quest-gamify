<?php

namespace App\Livewire\Admin\Priorities;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\TaskPriority;
use App\Services\Admin\PriorityService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;

    public string $name = '';

    public string $multiplier = '1.00';

    public function mount(): void
    {
        $this->authorize('create', TaskPriority::class);
    }

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

        $priority = app(PriorityService::class)->create($validated);

        $this->toastSuccess('Prioridade criada', "\"{$priority->name}\" foi criada.");
        $this->flushToasts();

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
