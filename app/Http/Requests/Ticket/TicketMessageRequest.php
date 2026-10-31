<?php

namespace App\Http\Requests\Ticket;

use App\Http\Requests\Concerns\ValidatesAttachments;
use Illuminate\Foundation\Http\FormRequest;

class TicketMessageRequest extends FormRequest
{
    use ValidatesAttachments;

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:2', 'max:5000'],
            'is_internal' => ['nullable', 'boolean'],
            ...$this->attachmentRules(required: false),
        ];
    }

    public function isInternal(): bool
    {
        return $this->boolean('is_internal');
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['body' => 'mensagem', 'is_internal' => 'tipo da mensagem', ...$this->attachmentAttributes()];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->attachmentMessages();
    }
}
