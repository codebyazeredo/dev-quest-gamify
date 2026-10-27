<?php

namespace App\Http\Requests\Board;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ColumnMilestoneRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'integer', Rule::enum(TaskStatus::class)],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['status' => 'marco de pontuação'];
    }

    public function milestone(): ?TaskStatus
    {
        $status = $this->validated('status');

        return $status !== null ? TaskStatus::from((int) $status) : null;
    }
}
