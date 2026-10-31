<?php

namespace App\Http\Requests\Ticket;

use App\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketPriorityRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['priority' => ['required', Rule::enum(TicketPriority::class)]];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['priority' => 'prioridade'];
    }

    public function priority(): TicketPriority
    {
        return TicketPriority::from($this->validated('priority'));
    }
}
