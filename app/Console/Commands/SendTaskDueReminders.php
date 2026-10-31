<?php

namespace App\Console\Commands;

use App\Services\TaskDueReminderService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tasks:send-due-reminders')]
#[Description('Avisa os responsáveis de tarefas com prazo vencendo nas próximas 24 horas ou já vencido')]
class SendTaskDueReminders extends Command
{
    public function handle(TaskDueReminderService $reminders): int
    {
        $this->info("{$reminders->sendPending()} lembrete(s) enviado(s).");

        return self::SUCCESS;
    }
}
