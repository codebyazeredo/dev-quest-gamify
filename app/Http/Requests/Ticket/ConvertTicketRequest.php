<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class ConvertTicketRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'column_id' => ['required', 'integer', 'exists:board_columns,id'],
            'category_id' => ['required', 'exists:task_categories,id'],
            'priority_id' => ['required', 'exists:task_priorities,id'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['column_id' => 'coluna', 'category_id' => 'categoria', 'priority_id' => 'prioridade'];
    }
}
