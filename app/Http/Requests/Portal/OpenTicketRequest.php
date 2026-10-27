<?php

namespace App\Http\Requests\Portal;

use App\Http\Requests\Concerns\ValidatesAttachments;
use Illuminate\Foundation\Http\FormRequest;

class OpenTicketRequest extends FormRequest
{
    use ValidatesAttachments;

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            ...$this->attachmentRules(required: false),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['title' => 'assunto', 'description' => 'descrição', ...$this->attachmentAttributes()];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->attachmentMessages();
    }
}
