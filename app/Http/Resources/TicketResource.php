<?php

namespace App\Http\Resources;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/** @mixin Ticket */
class TicketResource extends JsonResource
{
    private const SLA_WARNING_HOURS = 4;

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'client' => $this->client?->razao_social,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'priority' => $this->priority->value,
            'priority_label' => $this->priority->label(),
            'priority_color' => $this->priority->color(),
            'due_at' => $this->due_at?->format('d/m/Y H:i'),
            'is_overdue' => $this->isOverdue(),
            'sla_state' => $this->slaState(),
            'sla_label' => $this->slaLabel(),
            'assignee' => $this->whenLoaded('assignedTo', fn () => $this->assignedTo === null ? null : [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
                'initials' => $this->assignedTo->initials(),
                'photo_url' => $this->assignedTo->photo_url,
            ]),
            'messages_count' => $this->whenCounted('messages'),
            'last_activity' => $this->lastActivityLabel(),
            'has_task' => $this->task_id !== null,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'url' => route('tickets.show', $this->resource),
        ];
    }

    private function slaState(): string
    {
        return match (true) {
            $this->status === TicketStatus::RESOLVED || $this->first_responded_at !== null => 'done',
            $this->due_at === null => 'none',
            $this->due_at->isPast() => 'overdue',
            $this->due_at->diffInHours(now()) < self::SLA_WARNING_HOURS => 'soon',
            default => 'ok',
        };
    }

    private function slaLabel(): ?string
    {
        if ($this->status === TicketStatus::RESOLVED) {
            return null;
        }

        if ($this->first_responded_at !== null) {
            return 'Respondido';
        }

        if ($this->due_at === null) {
            return null;
        }

        $relative = $this->due_at->locale('pt_BR')->diffForHumans(['parts' => 1]);

        return $this->due_at->isPast() ? "Venceu {$relative}" : "Vence {$relative}";
    }

    private function lastActivityLabel(): string
    {
        $lastMessageAt = $this->resource->getAttribute('last_message_at');
        $lastActivity = $lastMessageAt !== null ? Carbon::parse($lastMessageAt) : $this->created_at;

        return $lastActivity->locale('pt_BR')->diffForHumans(['parts' => 1]);
    }
}
