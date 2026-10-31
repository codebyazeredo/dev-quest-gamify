<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Enums\XpSourceType;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_xp_audit_export_respects_filters_and_uses_excel_friendly_format(): void
    {
        $maria = User::factory()->developer()->create(['name' => 'Maria Dev']);
        $joao = User::factory()->developer()->create(['name' => 'Joao Dev']);
        XpTransaction::factory()->create(['user_id' => $maria->id, 'amount' => 10, 'source_type' => XpSourceType::CHECKIN, 'description' => 'Check-in diário']);
        XpTransaction::factory()->create(['user_id' => $joao->id, 'amount' => 7, 'source_type' => XpSourceType::CHECKIN, 'description' => 'Check-in do João']);

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.xp-audit.export', ['user_id' => $maria->id]));

        $response->assertOk()->assertDownload();
        $content = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Data;Usuário;XP;Origem;Motivo', $content);
        $this->assertContains(['Maria Dev', '10', 'Check-in', 'Check-in diário'], $this->parsedRows($content, 1, 4));
        $this->assertStringNotContainsString('Joao Dev', $content);
    }

    public function test_formulas_in_cells_are_neutralized(): void
    {
        $user = User::factory()->developer()->create(['name' => '=HYPERLINK("http://x")']);
        XpTransaction::factory()->create(['user_id' => $user->id, 'amount' => 1, 'description' => '+cmd']);

        $content = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.xp-audit.export'))
            ->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $content);
        $this->assertStringContainsString("'+cmd", $content);
    }

    public function test_only_admin_exports_the_xp_audit(): void
    {
        $this->actingAs(User::factory()->developer()->create())
            ->get(route('admin.xp-audit.export'))
            ->assertForbidden();
    }

    public function test_tickets_export_respects_status_filter(): void
    {
        Ticket::factory()->create(['title' => 'Chamado aberto', 'status' => TicketStatus::OPEN, 'client_id' => Client::factory()->create(['razao_social' => 'Acme Ltda'])]);
        Ticket::factory()->create(['title' => 'Chamado resolvido', 'status' => TicketStatus::RESOLVED]);

        $content = $this->actingAs(User::factory()->suporte()->create())
            ->get(route('tickets.export', ['status' => 'open']))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('Nº;Assunto;Cliente;Situação;Prioridade', $content);
        $this->assertContains(['Chamado aberto', 'Acme Ltda', 'Aberto', 'Normal'], $this->parsedRows($content, 1, 4));
        $this->assertStringNotContainsString('Chamado resolvido', $content);
    }

    public function test_developers_cannot_export_tickets(): void
    {
        $this->actingAs(User::factory()->developer()->create())
            ->get(route('tickets.export'))
            ->assertForbidden();
    }

    /** @return array<int, array<int, string>> linhas sem o cabeçalho, com $length colunas a partir de $start */
    private function parsedRows(string $content, int $start, int $length): array
    {
        $lines = array_slice(preg_split('/\R/', trim($content)), 1);

        return array_map(fn (string $line) => array_slice(str_getcsv($line, ';'), $start, $length), $lines);
    }
}
