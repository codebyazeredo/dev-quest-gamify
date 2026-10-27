<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\Concerns\ValidatesAttachments;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskAttachmentsRequest extends FormRequest
{
    use ValidatesAttachments;

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return $this->attachmentRules(required: true);
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return $this->attachmentAttributes();
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->attachmentMessages();
    }
}
