<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketMessageRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return ['body' => ['required', 'string', 'min:2', 'max:5000']];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['body' => 'mensagem'];
    }
}
