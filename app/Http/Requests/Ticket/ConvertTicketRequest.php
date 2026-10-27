<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConvertTicketRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'board_id' => ['required', 'integer', 'exists:boards,id'],
            'column_id' => ['required', 'integer', Rule::exists('board_columns', 'id')->where('board_id', $this->input('board_id'))],
            'title' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:task_categories,id'],
            'priority_id' => ['required', 'exists:task_priorities,id'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['board_id' => 'quadro', 'column_id' => 'coluna', 'title' => 'título', 'category_id' => 'categoria', 'priority_id' => 'prioridade'];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['column_id.exists' => 'A coluna escolhida não pertence ao quadro selecionado.'];
    }
}
