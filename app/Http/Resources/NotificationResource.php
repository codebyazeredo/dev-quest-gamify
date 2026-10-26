<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;

class NotificationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->data['kind'] ?? 'moved',
            'title' => $this->data['title'] ?? 'Tarefa',
            'message' => $this->data['message'] ?? '',
            'task_id' => $this->data['task_id'] ?? null,
            'is_read' => $this->read_at !== null,
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}
