<?php

namespace App\Livewire\Board;

use App\Enums\TaskStatus;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Board;
use App\Models\BoardColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public Board $board;

    public string $name;

    public string $description;

    public bool $is_active;

    public string $newColumnName = '';

    public int $newColumnStatus = 1;

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

    public function addColumn(): void
    {
        $this->authorize('update', $this->board);

        $this->validate([
            'newColumnName' => ['required', 'string', 'max:60'],
            'newColumnStatus' => ['required', 'integer'],
        ]);

        $status = TaskStatus::from($this->newColumnStatus);

        $this->board->columns()->create([
            'name' => $this->newColumnName,
            'slug' => Str::slug($this->newColumnName).'-'.$this->board->columns()->count(),
            'position' => $this->board->columns()->count(),
            'is_final' => $status === TaskStatus::DONE,
            'status' => $status,
        ]);

        $this->newColumnName = '';
        $this->board->refresh();

        $this->toastSuccess('Coluna adicionada', 'A coluna foi criada.');
        $this->flushToasts();
    }

    public function renameColumn(int $columnId, string $name): void
    {
        $this->authorize('update', $this->board);

        $column = $this->board->columns()->findOrFail($columnId);
        $column->update(['name' => $name]);

        $this->board->refresh();

        $this->toastSuccess('Coluna renomeada', 'O nome da coluna foi atualizado.');
        $this->flushToasts();
    }

    public function setColumnStatus(int $columnId, int $status): void
    {
        $this->authorize('update', $this->board);

        $column = $this->board->columns()->findOrFail($columnId);
        $status = TaskStatus::from($status);

        $column->update([
            'status' => $status,
            'is_final' => $status === TaskStatus::DONE,
        ]);

        $this->board->refresh();

        $this->toastSuccess('Status da coluna atualizado', 'A coluna foi reclassificada.');
        $this->flushToasts();
    }

    public function moveColumnUp(int $columnId): void
    {
        $this->authorize('update', $this->board);

        $this->swapColumnPosition($columnId, -1);
    }

    public function moveColumnDown(int $columnId): void
    {
        $this->authorize('update', $this->board);

        $this->swapColumnPosition($columnId, 1);
    }

    protected function swapColumnPosition(int $columnId, int $direction): void
    {
        DB::transaction(function () use ($columnId, $direction) {
            $columns = $this->board->columns()->orderBy('position')->get();
            $index = $columns->search(fn (BoardColumn $column) => $column->id === $columnId);

            $targetIndex = $index + $direction;

            if ($index === false || $targetIndex < 0 || $targetIndex >= $columns->count()) {
                return;
            }

            $current = $columns[$index];
            $target = $columns[$targetIndex];

            [$currentPosition, $targetPosition] = [$current->position, $target->position];

            $current->update(['position' => $targetPosition]);
            $target->update(['position' => $currentPosition]);
        });

        $this->board->refresh();
    }

    public function deleteColumn(int $columnId): void
    {
        $this->authorize('update', $this->board);

        $column = $this->board->columns()->findOrFail($columnId);

        if ($column->tasks()->exists()) {
            $this->addError('columns', 'Não é possível excluir uma coluna que ainda possui tarefas.');
            $this->toastError('Não foi possível excluir', 'Esta coluna ainda possui tarefas.');
            $this->flushToasts();

            return;
        }

        $column->delete();

        $this->board->columns()->orderBy('position')->get()->values()
            ->each(fn (BoardColumn $column, int $index) => $column->update(['position' => $index]));

        $this->board->refresh();

        $this->toastSuccess('Coluna excluída', 'A coluna foi removida do quadro.');
        $this->flushToasts();
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.board.edit', [
            'statuses' => TaskStatus::cases(),
        ]);
    }
}
