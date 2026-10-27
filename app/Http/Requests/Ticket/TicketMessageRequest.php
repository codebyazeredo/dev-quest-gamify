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
            ...$this->attachmentRules(required: false),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['body' => 'mensagem', ...$this->attachmentAttributes()];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->attachmentMessages();
    }
}
