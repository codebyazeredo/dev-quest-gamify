<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedMail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Task $task, private User $actor) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return $notifiable->email_notifications ? ['mail'] : [];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nova tarefa atribuída a você: {$this->task->title}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("{$this->actor->name} atribuiu a tarefa \"{$this->task->title}\" a você.")
            ->when($this->task->due_at, fn (MailMessage $message) => $message->line("Prazo: {$this->task->due_at->format('d/m/Y H:i')}."))
            ->action('Abrir tarefa', route('tasks.show', $this->task));
    }
}
