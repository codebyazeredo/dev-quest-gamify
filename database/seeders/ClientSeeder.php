<?php

namespace Database\Seeders;

use App\Enums\TicketStatus;
use App\Models\BusinessSector;
use App\Models\Client;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use App\Services\AddressService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ClientSeeder extends Seeder
{
    private const CLIENTS = [
        [
            'cnpj' => '11222333000181',
            'razao_social' => 'Mercado Bom Preço Ltda',
            'email' => 'ti@bompreco.test',
            'sector' => 'Varejo e Comércio',
            'address' => ['cep' => '01310-100', 'logradouro' => 'Avenida Paulista', 'numero' => '1578', 'bairro' => 'Bela Vista', 'cidade' => 'São Paulo', 'estado' => 'SP'],
        ],
        [
            'cnpj' => '22845566000157',
            'razao_social' => 'Clínica Vida Plena S.A.',
            'email' => 'contato@vidaplena.test',
            'sector' => 'Saúde',
            'address' => ['cep' => '30130-010', 'logradouro' => 'Rua da Bahia', 'numero' => '1148', 'complemento' => 'Sala 302', 'bairro' => 'Centro', 'cidade' => 'Belo Horizonte', 'estado' => 'MG'],
        ],
        [
            'cnpj' => '33456789000149',
            'razao_social' => 'Colégio Horizonte Educacional',
            'email' => 'secretaria@horizonte.test',
            'sector' => 'Educação',
            'address' => ['cep' => '80010-000', 'logradouro' => 'Rua XV de Novembro', 'numero' => '700', 'bairro' => 'Centro', 'cidade' => 'Curitiba', 'estado' => 'PR'],
        ],
        [
            'cnpj' => '44567890000193',
            'razao_social' => 'TransRápido Logística',
            'email' => 'operacoes@transrapido.test',
            'sector' => 'Logística e Transporte',
            'address' => ['cep' => '90010-150', 'logradouro' => 'Avenida Borges de Medeiros', 'numero' => '2244', 'referencia' => 'Próximo à Usina do Gasômetro', 'bairro' => 'Praia de Belas', 'cidade' => 'Porto Alegre', 'estado' => 'RS'],
        ],
        [
            'cnpj' => '55678901000129',
            'razao_social' => 'Fazenda Três Lagoas Agro',
            'email' => 'administrativo@treslagoas.test',
            'sector' => 'Agronegócio',
            'address' => ['cep' => '50030-230', 'logradouro' => 'Avenida Conde da Boa Vista', 'numero' => '800', 'bairro' => 'Boa Vista', 'cidade' => 'Recife', 'estado' => 'PE'],
        ],
        [
            'cnpj' => '66789012000155',
            'razao_social' => 'Banco Digital Aurora',
            'email' => 'suporte@aurora.test',
            'sector' => 'Serviços Financeiros',
            'address' => ['cep' => '01310-100', 'logradouro' => 'Avenida Paulista', 'numero' => '1578', 'complemento' => '12º andar', 'bairro' => 'Bela Vista', 'cidade' => 'São Paulo', 'estado' => 'SP'],
        ],
    ];

    private const TICKETS = [
        [
            'client' => 0,
            'title' => 'Erro ao fechar o caixa no fim do dia',
            'description' => "Ao fechar o caixa aparece a mensagem 'Falha ao consolidar vendas' e o relatório não é gerado.\nAcontece desde segunda-feira em todas as lojas.",
            'status' => TicketStatus::IN_PROGRESS,
            'daysAgo' => 6,
            'linkedTask' => true,
            'messages' => [
                [false, 'Segue o print do erro. Acontece principalmente depois das 18h.'],
                [true, 'Obrigado pelo aviso! Já reproduzimos o problema e abrimos uma tarefa para a correção.'],
            ],
        ],
        [
            'client' => 1,
            'title' => 'Solicitação de novo relatório de agendamentos',
            'description' => 'Precisamos de um relatório mensal com o total de consultas por especialidade e por médico.',
            'status' => TicketStatus::OPEN,
            'daysAgo' => 3,
            'messages' => [],
        ],
        [
            'client' => 2,
            'title' => 'Não consigo redefinir a senha de um professor',
            'description' => 'O e-mail de redefinição não chega para o professor Carlos. Já conferimos a caixa de spam.',
            'status' => TicketStatus::RESOLVED,
            'daysAgo' => 12,
            'messages' => [
                [true, 'O e-mail cadastrado estava com um erro de digitação. Corrigimos e reenviamos o link.'],
                [false, 'Perfeito, ele conseguiu acessar. Obrigado!'],
            ],
        ],
        [
            'client' => 3,
            'title' => 'Rastreamento não atualiza a posição das entregas',
            'description' => 'As entregas ficam paradas na mesma posição por horas, mesmo com o motorista em movimento.',
            'status' => TicketStatus::OPEN,
            'daysAgo' => 1,
            'messages' => [
                [true, 'Pode nos informar o modelo do aparelho usado pelos motoristas?'],
            ],
        ],
        [
            'client' => 4,
            'title' => 'Dúvida sobre exportação de dados da safra',
            'description' => 'Como exportar o histórico da safra 2025 em planilha para enviar ao contador?',
            'status' => TicketStatus::RESOLVED,
            'daysAgo' => 20,
            'messages' => [
                [true, 'Na tela de Safras, use o botão Exportar no canto superior direito e escolha o formato XLSX.'],
            ],
        ],
        [
            'client' => 5,
            'title' => 'Lentidão na tela de extrato',
            'description' => 'O extrato com mais de 90 dias leva mais de 30 segundos para carregar.',
            'status' => TicketStatus::OPEN,
            'daysAgo' => 0,
            'messages' => [],
        ],
    ];

    public function run(): void
    {
        $addresses = app(AddressService::class);

        $clients = collect(self::CLIENTS)->map(fn (array $data) => Client::updateOrCreate(
            ['cnpj' => $data['cnpj']],
            [
                'razao_social' => $data['razao_social'],
                'email' => $data['email'],
                'is_active' => true,
                'business_sector_id' => BusinessSector::where('name', $data['sector'])->value('id'),
                'address_id' => $addresses->resolve($data['address'])->id,
            ],
        ));

        if (! Ticket::query()->exists()) {
            $this->seedTickets($clients);
        }
    }

    /** @param  Collection<int, Client>  $clients */
    private function seedTickets(Collection $clients): void
    {
        $support = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'suporte'))->first();
        $task = Task::query()->orderBy('id')->first();

        foreach (self::TICKETS as $data) {
            $ticket = Ticket::create([
                'client_id' => $clients[$data['client']]->id,
                'task_id' => ($data['linkedTask'] ?? false) ? $task?->id : null,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status'],
            ]);

            $ticket->forceFill(['created_at' => now()->subDays($data['daysAgo'])])->save();

            foreach ($data['messages'] as $index => [$fromStaff, $body]) {
                if ($fromStaff && $support === null) {
                    continue;
                }

                $ticket->messages()->create(['user_id' => $fromStaff ? $support->id : null, 'body' => $body])
                    ->forceFill(['created_at' => now()->subDays($data['daysAgo'])->addHours($index + 1)])
                    ->save();
            }
        }
    }
}
