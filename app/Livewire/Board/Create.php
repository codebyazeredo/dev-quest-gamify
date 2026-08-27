<?php

namespace App\Livewire\Board;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\Board;
use App\Models\BoardColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;

    public string $name = '';

    public string $description = '';

    public bool $is_active = true;

    public function mount(): void
    {
        $this->authorize('create', Board::class);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', 'unique:boards,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Board::class);

        $validated = $this->validate();

        $board = DB::transaction(function () use ($validated) {
            $board = Board::create($validated);

            BoardColumn::seedDefaultsFor($board);

            return $board;
        });

        $this->toastSuccess('Quadro criado', "\"{$board->name}\" foi criado.");

        $this->redirectRoute('boards.show', ['board' => $board]);
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.board.create');
    }
}
