<?php

namespace App\Livewire\Board;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\Board;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public Board $board;

    public string $name;

    public string $description;

    public bool $is_active;

    public function mount(Board $board): void
    {
        $this->authorize('update', $board);

        $this->board = $board;
        $this->name = $board->name;
        $this->description = (string) $board->description;
        $this->is_active = $board->is_active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', 'unique:boards,name,'.$this->board->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->board);

        $validated = $this->validate();

        $this->board->update($validated);

        $this->toastSuccess('Quadro atualizado', 'As alterações foram salvas.');
        $this->flushToasts();

        $this->dispatch('board-updated');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.board.edit');
    }
}
