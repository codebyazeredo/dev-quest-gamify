<?php

namespace Tests\Feature\Portal;

use App\Http\Middleware\EnsurePortalClient;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Support\StoredFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortalTicketAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(StoredFile::DISK);
    }

    private function signedIn(Client $client): static
    {
        return $this->withSession([EnsurePortalClient::SESSION_KEY => $client->id]);
    }

    public function test_client_attaches_files_when_opening_a_ticket(): void
    {
        $client = Client::factory()->create();

        $this->signedIn($client)
            ->post(route('portal.tickets.store'), [
                'title' => 'Erro na emissão',
                'description' => 'Segue o print da tela de erro.',
                'files' => [UploadedFile::fake()->image('erro.png'), UploadedFile::fake()->create('log.txt', 5, 'text/plain')],
            ])
            ->assertRedirect();

        $ticket = Ticket::firstOrFail();

        $this->assertSame(2, $ticket->attachments()->count());
        $this->assertNull($ticket->attachments()->first()->user_id);

        foreach (TicketAttachment::all() as $attachment) {
            Storage::disk(StoredFile::DISK)->assertExists($attachment->path);
        }
    }

    public function test_client_attaches_files_to_a_reply(): void
    {
        $client = Client::factory()->create();
        $ticket = Ticket::factory()->for($client)->create();

        $this->signedIn($client)
            ->post(route('portal.tickets.reply', $ticket), [
                'body' => 'Novo print.',
                'files' => [UploadedFile::fake()->image('novo.png')],
            ])
            ->assertRedirect(route('portal.tickets.show', $ticket));

        $attachment = TicketAttachment::firstOrFail();

        $this->assertNotNull($attachment->ticket_message_id);
        $this->assertSame($ticket->id, $attachment->ticket_id);
    }

    public function test_invalid_files_are_rejected(): void
    {
        $client = Client::factory()->create();

        $this->signedIn($client)
            ->post(route('portal.tickets.store'), [
                'title' => 'Chamado com arquivo ruim',
                'description' => 'Tentando enviar um executável.',
                'files' => [UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')],
            ])
            ->assertSessionHasErrors('files.0');

        $this->assertSame(0, Ticket::count());
    }

    public function test_only_the_owner_company_downloads_the_attachment(): void
    {
        $client = Client::factory()->create();
        $ticket = Ticket::factory()->for($client)->create();
        $attachment = TicketAttachment::factory()->for($ticket)->create();
        Storage::disk(StoredFile::DISK)->put($attachment->path, 'conteudo');

        $this->signedIn($client)->get(route('portal.attachments.show', $attachment))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->signedIn(Client::factory()->create())->get(route('portal.attachments.show', $attachment))->assertNotFound();

        $this->get(route('portal.attachments.show', $attachment))->assertRedirect(route('portal.login'));
    }
}
