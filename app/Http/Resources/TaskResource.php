<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
class TaskResource extends JsonResource
{
    private const DATETIME_INPUT_FORMAT = 'Y-m-d\TH:i';

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'priority_id' => $this->priority_id,
            'due_at' => $this->due_at?->format(self::DATETIME_INPUT_FORMAT),
            'branch_name' => $this->branch_name,
            'pull_request_url' => $this->pull_request_url,
            'locked' => $this->completed_at !== null,
            'column_id' => $this->column_id,
            'position' => $this->position,
            'status' => $this->status?->value,
        ];
    }
}
