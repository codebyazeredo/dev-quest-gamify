<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(TaskAttachment::DISK);
    }

    private function storedAttachment(string $state = 'image', ?User $uploader = null, ?Task $task = null): TaskAttachment
    {
        $attachment = TaskAttachment::factory()
            ->{$state}()
            ->for($task ?? Task::factory()->create())
            ->create(['user_id' => ($uploader ?? User::factory()->developer()->create())->id]);

        Storage::disk(TaskAttachment::DISK)->put($attachment->path, 'conteudo');

        return $attachment;
    }

    public function test_any_user_uploads_several_files_to_a_task(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create();

        $this->actingAs($developer)
            ->postJson(route('api.tasks.attachments.store', $task), [
                'files' => [UploadedFile::fake()->image('print.png'), UploadedFile::fake()->create('relatorio.pdf', 200, 'application/pdf')],
            ])
            ->assertCreated()
            ->assertJsonPath('message', '2 anexos adicionados.');

        $this->assertSame(2, $task->attachments()->count());

        $attachment = $task->attachments()->where('original_name', 'relatorio.pdf')->firstOrFail();

        $this->assertSame($developer->id, $attachment->user_id);
        $this->assertSame('application/pdf', $attachment->mime_type);
        $this->assertGreaterThan(0, $attachment->size);
        Storage::disk(TaskAttachment::DISK)->assertExists($attachment->path);
    }

    public function test_disallowed_oversized_and_missing_files_are_rejected(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create();
        $url = route('api.tasks.attachments.store', $task);

        $this->actingAs($developer)
            ->postJson($url, ['files' => [UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('files.0');

        $this->actingAs($developer)
            ->postJson($url, ['files' => [UploadedFile::fake()->create('grande.pdf', 10241, 'application/pdf')]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('files.0');

        $this->actingAs($developer)->postJson($url, [])->assertUnprocessable()->assertJsonValidationErrors('files');

        $this->assertSame(0, TaskAttachment::count());
    }

    public function test_no_more_than_ten_files_per_upload(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create();
        $files = array_map(fn ($i) => UploadedFile::fake()->create("doc{$i}.pdf", 10, 'application/pdf'), range(1, 11));

        $this->actingAs($developer)
            ->postJson(route('api.tasks.attachments.store', $task), ['files' => $files])
            ->assertUnprocessable()
            ->assertJsonPath('errors.files.0', 'Envie no máximo 10 anexos por vez.');
    }

    public function test_archived_tasks_do_not_accept_attachments(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['archived_at' => now()]);

        $this->actingAs($developer)
            ->postJson(route('api.tasks.attachments.store', $task), ['files' => [UploadedFile::fake()->image('print.png')]])
            ->assertForbidden();
    }

    public function test_images_open_inline_and_other_files_are_downloaded(): void
    {
        $developer = User::factory()->developer()->create();
        $image = $this->storedAttachment('image');
        $text = $this->storedAttachment('text');

        $this->actingAs($developer)->get(route('attachments.show', $image))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Disposition', 'inline; filename='.$image->original_name);

        $this->actingAs($developer)->get(route('attachments.show', $text))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename='.$text->original_name);

        $this->actingAs($developer)->get(route('attachments.show', [$image, 'download' => 1]))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename='.$image->original_name);
    }

    public function test_guests_cannot_download_and_missing_files_return_not_found(): void
    {
        $attachment = $this->storedAttachment('image');

        $this->get(route('attachments.show', $attachment))->assertRedirect(route('login'));

        Storage::disk(TaskAttachment::DISK)->delete($attachment->path);

        $this->actingAs(User::factory()->developer()->create())
            ->get(route('attachments.show', $attachment))
            ->assertNotFound();
    }

    public function test_uploader_and_admin_can_remove_but_other_users_cannot(): void
    {
        $uploader = User::factory()->developer()->create();
        $other = User::factory()->developer()->create();
        $admin = User::factory()->admin()->create();
        $attachment = $this->storedAttachment('image', $uploader);

        $this->actingAs($other)->deleteJson(route('api.attachments.destroy', $attachment))->assertForbidden();
        $this->assertNotNull(TaskAttachment::find($attachment->id));

        $this->actingAs($uploader)->deleteJson(route('api.attachments.destroy', $attachment))->assertOk();

        $this->assertNull(TaskAttachment::find($attachment->id));
        Storage::disk(TaskAttachment::DISK)->assertMissing($attachment->path);

        $byOther = $this->storedAttachment('text', $other);

        $this->actingAs($admin)->deleteJson(route('api.attachments.destroy', $byOther))->assertOk();
        $this->assertNull(TaskAttachment::find($byOther->id));
    }

    public function test_task_page_lists_attachments_and_board_card_shows_the_count(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id]);
        $first = $this->storedAttachment('image', $admin, $task);
        $this->storedAttachment('text', $admin, $task);

        $this->actingAs($admin)->get(route('tasks.show', $task))
            ->assertOk()
            ->assertSee($first->original_name)
            ->assertSee(route('attachments.show', $first), false);

        $this->actingAs($admin)->get(route('boards.show', $board))
            ->assertOk()
            ->assertSee('title="2 anexos"', false);
    }

    public function test_human_readable_size_and_icon(): void
    {
        $this->assertSame('512 B', TaskAttachment::factory()->make(['size' => 512])->human_size);
        $this->assertSame('1,5 KB', TaskAttachment::factory()->make(['size' => 1536])->human_size);
        $this->assertSame('2,0 MB', TaskAttachment::factory()->make(['size' => 2 * 1024 * 1024])->human_size);
        $this->assertSame('bi-file-earmark-pdf', TaskAttachment::factory()->make()->icon());
        $this->assertSame('bi-file-earmark-image', TaskAttachment::factory()->image()->make()->icon());
    }
}
