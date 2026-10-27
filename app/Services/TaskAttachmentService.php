<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use App\Repositories\TaskAttachmentRepository;
use App\Support\StoredFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentService
{
    public function __construct(private TaskAttachmentRepository $attachments) {}

    /**
     * @param  array<int, UploadedFile>  $files
     * @return Collection<int, TaskAttachment>
     */
    public function store(Task $task, User $user, array $files): Collection
    {
        return collect($files)->map(fn (UploadedFile $file) => $this->attachments->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            ...StoredFile::store($file, "task-attachments/{$task->id}"),
        ]));
    }

    public function delete(TaskAttachment $attachment): void
    {
        Storage::disk(TaskAttachment::DISK)->delete($attachment->path);

        $this->attachments->delete($attachment);
    }
}
