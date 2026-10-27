<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

/** Valida criação (informa a coluna) e edição dos detalhes de uma tarefa. */
class TaskRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'column_id' => $this->isMethod('POST') ? ['required', 'integer', 'exists:board_columns,id'] : ['prohibited'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:task_categories,id'],
            'priority_id' => ['required', 'exists:task_priorities,id'],
            'assigned_to' => $this->isMethod('POST') ? ['nullable', 'exists:users,id'] : ['prohibited'],
            'due_at' => ['nullable', 'date'],
            'branch_name' => ['nullable', 'string', 'max:120', 'regex:/^[\w\-\.\/]+$/'],
            'pull_request_url' => ['nullable', 'url:http,https', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'column_id' => 'coluna',
            'title' => 'título',
            'description' => 'descrição',
            'category_id' => 'categoria',
            'priority_id' => 'prioridade',
            'assigned_to' => 'responsável',
            'due_at' => 'prazo',
            'branch_name' => 'branch',
            'pull_request_url' => 'link do pull request',
        ];
    }
}
