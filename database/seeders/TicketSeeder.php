<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Client;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Chamados dos clientes do TMS com o atendimento completo: respostas da equipe, notas internas,
 * chamados convertidos em tarefa, aguardando o cliente, em andamento e com prazo de SLA vencido.
 *
 * Mensagens: c = cliente, s = responsável pelo chamado, p = product owner, n = nota interna.
 * Cada mensagem traz as horas decorridas desde a abertura do chamado.
 */
class TicketSeeder extends Seeder
{
    private const SLA_HOURS = ['low' => 72, 'normal' => 48, 'high' => 24, 'urgent' => 4];

    private const STAFF_EMAILS = [
        'sup1' => 'suporte@devquestgamify.test',
        'sup2' => 'diego.martins@devquestgamify.test',
        'po' => 'po@devquestgamify.test',
    ];

    public function run(): void
    {
        if (Ticket::query()->exists()) {
            return;
        }

        $clients = Client::orderBy('id')->get();
        $staff = collect(self::STAFF_EMAILS)->map(fn (string $email) => User::where('email', $email)->firstOrFail());
        $now = Carbon::now();

        $tickets = $this->tickets();
        usort($tickets, fn (array $first, array $second) => $second[5] <=> $first[5]);

        foreach ($tickets as $data) {
            [$clientIndex, $title, $description, $priority, $status, $daysAgo, $who, $taskTitle, $messages] = $data;

            $createdAt = $now->copy()->subHours((int) round($daysAgo * 24));
            $assignee = $who !== null ? $staff[$who] : null;
            $firstResponse = collect($messages)->first(fn (array $message) => in_array($message[0], ['s', 'p'], true));
            $task = $taskTitle !== null ? Task::where('title', $taskTitle)->first() : null;

            $ticket = Ticket::create([
                'client_id' => $clients[$clientIndex]->id,
                'task_id' => $task?->id,
                'assigned_to' => $assignee?->id,
                'title' => $title,
                'description' => $description,
                'status' => TicketStatus::from($status),
                'priority' => TicketPriority::from($priority),
                'due_at' => $createdAt->copy()->addHours(self::SLA_HOURS[$priority]),
                'first_responded_at' => $firstResponse !== null ? $createdAt->copy()->addMinutes((int) round($firstResponse[1] * 60)) : null,
            ]);
            $ticket->forceFill(['created_at' => $createdAt, 'updated_at' => $this->lastActivity($createdAt, $messages)])->save();

            foreach ($messages as [$type, $hours, $body]) {
                $author = match ($type) {
                    'c' => null,
                    'p' => $staff['po'],
                    default => $assignee ?? $staff['sup1'],
                };

                $ticket->messages()->create(['user_id' => $author?->id, 'body' => $body, 'is_internal' => $type === 'n'])
                    ->forceFill(['created_at' => $createdAt->copy()->addMinutes((int) round($hours * 60))])
                    ->save();
            }

            if ($task !== null) {
                $task->update(['description' => "Chamado #{$ticket->id} — {$clients[$clientIndex]->razao_social}\n\n{$description}"]);
            }
        }
    }

    /** @param  array<int, array{0: string, 1: float, 2: string}>  $messages */
    private function lastActivity(Carbon $createdAt, array $messages): Carbon
    {
        $lastHours = collect($messages)->max(fn (array $message) => $message[1]) ?? 0;

        return $createdAt->copy()->addMinutes((int) round($lastHours * 60));
    }

    /**
     * @return array<int, array{0: int, 1: string, 2: string, 3: string, 4: string, 5: float, 6: ?string, 7: ?string, 8: array<int, array{0: string, 1: float, 2: string}>}>
     */
    private function tickets(): array
    {
        return [
            [0, "CT-e rejeitado: 'Rejeição 539 – Duplicidade de CT-e'", 'Desde ontem vários CT-e voltam com a rejeição 539 mesmo sendo a primeira emissão. A operação de Curitiba parou.', 'urgent', 'resolved', 58, 'sup1', null, [
                ['s', 0.3, 'Bom dia! Já estamos analisando. Pode nos enviar a chave de acesso de um dos CT-e rejeitados e a série utilizada?'],
                ['c', 0.8, 'Chave 4125 1012 3450 0001 5700 1000 0004 1210 0004 5671. Série 1, emitido pela filial Curitiba.'],
                ['n', 1.2, 'Numeração 41210 já autorizada em contingência no dia 12. A série da contingência estava igual à normal.'],
                ['s', 1.6, 'Encontramos a causa: houve uma emissão em contingência durante a queda da SEFAZ e a numeração ficou repetida. Ajustamos a sequência da série 1 e reprocessamos os documentos rejeitados.'],
                ['c', 2.4, 'Reprocessados aqui, todos autorizados. Obrigado pela rapidez!'],
                ['s', 2.6, 'Perfeito. Deixamos uma série exclusiva para contingência, assim isso não se repete. Vou encerrar o chamado.'],
            ]],
            [0, 'Relatório de frete por rota não bate com o financeiro', 'O total de frete do relatório por rota de outubro está R$ 18 mil menor que o lançado no financeiro.', 'normal', 'resolved', 49, 'sup2', null, [
                ['s', 2, 'Olá! Pode nos dizer quais rotas estão divergindo e se o relatório foi filtrado por data de emissão ou de entrega?'],
                ['c', 5, 'Filtrei por data de entrega. As rotas Curitiba–Joinville e Curitiba–Londrina são as que mais diferem.'],
                ['n', 6, 'Relatório usa data de entrega e o financeiro usa data de emissão do CT-e. Cargas emitidas em 31/10 e entregues em novembro ficaram de fora.'],
                ['s', 7, 'A diferença vem do critério de data: o relatório considera a entrega e o financeiro a emissão. Ao filtrar por emissão os valores batem. Vamos destacar o critério na tela para evitar confusão.'],
                ['c', 9, 'Entendi, conferido por emissão e fechou. Obrigado!'],
            ]],
            [0, 'Campo de observação interna na ordem de coleta', 'Gostaríamos de registrar instruções internas na ordem de coleta que não apareçam para o motorista.', 'low', 'resolved', 22, 'sup1', 'Campo de observação interna na ordem de coleta', [
                ['s', 4, 'Boa tarde! Ótima sugestão. Registramos o pedido e já encaminhamos para o time de produto avaliar.'],
                ['p', 28, 'Aprovado para o próximo ciclo. Vamos incluir o campo na ordem de coleta e no romaneio interno.'],
                ['s', 30, 'Atualização: o pedido entrou no planejamento e já virou tarefa para o time de desenvolvimento.'],
                ['s', 240, 'Liberamos o campo de observação interna na versão desta semana. Ele aparece só para usuários internos. Qualquer ajuste, estamos à disposição!'],
                ['c', 242, 'Funcionando, era exatamente isso. Obrigada!'],
            ]],
            [1, 'MDF-e não encerra automaticamente após a entrega', 'Manifestos ficam abertos depois que todas as entregas são confirmadas e a emissão do próximo MDF-e é rejeitada.', 'urgent', 'in_progress', 3, 'sup1', 'Encerrar o MDF-e automaticamente após a confirmação da última entrega', [
                ['s', 0.4, 'Olá! Entendemos a urgência. Pode informar o número de um dos manifestos abertos?'],
                ['c', 0.9, 'MDF-e 18273, filial Caxias. Já temos 9 veículos parados aguardando emissão.'],
                ['n', 1.3, 'Reproduzimos: o encerramento só dispara na baixa manual; baixa pelo app não aciona. Abrindo tarefa crítica para dev.'],
                ['s', 1.5, 'Identificamos o problema: o encerramento automático não é disparado quando a baixa é feita pelo app do motorista. Abrimos uma correção prioritária. Enquanto isso, é possível encerrar manualmente em Manifestos > Encerrar.'],
                ['c', 2, 'Vamos encerrar manualmente por enquanto. Preciso de uma previsão.'],
                ['s', 20, 'A correção está em desenvolvimento e já em teste interno. Devemos liberar nas próximas 24 horas e avisamos por aqui.'],
            ]],
            [1, 'Erro ao importar XML de NF-e com mais de 500 itens', 'A importação em lote trava quando a nota tem muitos itens, como as de ferragens. O sistema fica carregando e nada é importado.', 'high', 'resolved', 41, 'sup2', 'Importação de XML de NF-e com muitos itens estoura o tempo limite', [
                ['s', 1, 'Olá! Pode anexar um dos XML que não importa? Vamos reproduzir em ambiente de teste.'],
                ['c', 3, 'Enviei por e-mail o XML da nota 88421, com 640 itens.'],
                ['s', 6, 'Reproduzimos. A importação estourava o tempo limite com notas grandes. Abrimos uma correção para processar os itens em partes.'],
                ['s', 160, 'Correção liberada. Notas com até 3 mil itens agora são importadas em poucos segundos. Pode reimportar o lote pendente?'],
                ['c', 164, 'Reimportei tudo, funcionou. Obrigado!'],
            ]],
            [2, 'Rastreamento sem posição para veículos Sascar', 'Cerca de 30 veículos da frota aparecem sem posição no mapa desde ontem à tarde.', 'high', 'waiting_customer', 4, 'sup1', null, [
                ['s', 0.5, 'Boa tarde! Vamos verificar. Os veículos sem posição são todos do mesmo modelo de rastreador?'],
                ['c', 1.4, 'Acho que sim, mas não tenho certeza. Posso conferir com o pessoal da frota.'],
                ['n', 2, 'Logs mostram resposta 401 da API do fornecedor para a conta deles a partir de ontem 15h. Possível troca de credencial.'],
                ['s', 2.2, 'Identificamos que a autenticação com a Sascar está retornando erro para a conta de vocês. Vocês renovaram ou trocaram a senha do portal da Sascar recentemente? Precisamos da nova credencial para atualizar a integração.'],
            ]],
            [2, 'Como cadastrar tabela de frete por faixa de peso e CEP?', 'Preciso cadastrar uma tabela com valores diferentes por faixa de peso e por região de CEP. Onde configuro?', 'low', 'resolved', 33, 'sup2', null, [
                ['s', 3, 'Olá! Em Cadastros > Tabelas de frete, crie a tabela e use a aba "Faixas". Cada faixa aceita peso mínimo e máximo, e a aba "Regiões" permite agrupar CEPs. Posso enviar um vídeo curto com o passo a passo, se preferir.'],
                ['c', 6, 'Consegui criar a tabela, mas como aplico à um cliente específico?'],
                ['s', 7, 'Basta vincular a tabela no cadastro do cliente, em Contratos > Tabela padrão. Dali em diante as cotações já usam essa tabela.'],
                ['c', 8, 'Perfeito, funcionou. Obrigado!'],
            ]],
            [2, 'Lentidão ao listar cargas com filtro por período', 'A tela de cargas demora mais de 20 segundos quando filtramos 30 dias.', 'normal', 'in_progress', 7, 'sup1', 'Listagem de cargas lenta com filtro por período', [
                ['s', 5, 'Olá! Obrigada por avisar. Pode nos dizer o horário em que a lentidão é maior?'],
                ['c', 22, 'Principalmente de manhã, entre 8h e 10h.'],
                ['n', 24, 'Coincide com o horário de maior volume de importação. Consulta de contagem total é o gargalo, dev já está olhando.'],
                ['s', 25, 'Encontramos o ponto de lentidão e já temos uma correção em revisão. Avisamos assim que for liberada.'],
            ]],
            [3, 'CIOT não é gerado para motoristas autônomos', 'Ao gerar a viagem para motorista autônomo o sistema retorna erro e não emite o CIOT. Não conseguimos liberar os caminhões.', 'urgent', 'resolved', 44, 'sup1', 'Corrigir a geração de CIOT para motoristas autônomos', [
                ['s', 0.2, 'Bom dia! Já estamos olhando com prioridade. Pode nos informar o CPF do motorista de um caso com erro?'],
                ['c', 0.7, 'Motorista Antônio, CPF final 2231. Ele tem dois cavalos cadastrados.'],
                ['n', 1, 'Erro acontece quando o motorista tem mais de um veículo ativo. Bug no vínculo veículo x CIOT. Escalando para dev (crítico).'],
                ['s', 1.2, 'Encontramos a causa: a geração falha quando o motorista tem mais de um veículo ativo. Já escalamos como crítico para o desenvolvimento.'],
                ['s', 20, 'Correção em produção. Os CIOT pendentes podem ser gerados novamente pela tela de viagens.'],
                ['c', 21, 'Gerou tudo. Muito obrigado pela agilidade!'],
            ]],
            [3, 'Pedido de acesso para novo usuário financeiro', 'Precisamos criar o acesso da nova analista financeira com permissão apenas de faturamento e contas a receber.', 'low', 'resolved', 27, 'sup2', null, [
                ['s', 2, 'Olá! Para criar o usuário precisamos do nome completo, e-mail e CPF da analista. O perfil de faturamento e contas a receber já existe.'],
                ['c', 20, 'Mariana Oliveira, mariana.oliveira@transcerrado.test, CPF 123.456.789-09.'],
                ['s', 21, 'Usuário criado com o perfil Financeiro. A senha provisória foi enviada ao e-mail informado e deve ser trocada no primeiro acesso.'],
            ]],
            [3, 'Divergência no valor do pedágio calculado na roteirização', 'O valor do pedágio estimado está abaixo do cobrado na estrada para carretas com eixo suspenso.', 'high', 'in_progress', 2, 'sup1', 'Cálculo de pedágio divergente na roteirização', [
                ['s', 1, 'Olá! Pode enviar um exemplo com origem, destino e a configuração do veículo?'],
                ['c', 3, 'Uberlândia para Santos, carreta de 6 eixos com 1 eixo suspenso. Estimado R$ 612, cobrado R$ 703.'],
                ['n', 4, 'Cálculo ignora o eixo suspenso, que cobra tarifa normal ao carregar. Tarefa já criada, vai para o júnior com apoio do sênior.'],
                ['s', 5, 'Reproduzimos a diferença: o eixo suspenso não estava sendo considerado. A correção já está em desenvolvimento.'],
            ]],
            [4, 'Averbação de seguro duplicada para o mesmo CT-e', 'Alguns CT-e aparecem averbados duas vezes na seguradora e a apólice cobra em duplicidade.', 'high', 'resolved', 36, 'sup2', null, [
                ['s', 2, 'Olá! Pode informar os números dos CT-e duplicados para localizarmos no log da integração?'],
                ['c', 4, 'CT-e 7701, 7702 e 7710.'],
                ['n', 5, 'Reenvio automático da averbação disparou duas vezes após timeout da seguradora. Falta checagem de duplicidade por chave.'],
                ['s', 6, 'A seguradora respondeu com atraso e o sistema reenviou a averbação. Ajustamos a integração para não reenviar quando o protocolo já existe e cancelamos os duplicados junto à seguradora.'],
                ['c', 27, 'Seguradora confirmou o cancelamento. Obrigado.'],
            ]],
            [4, 'Fatura gerada sem o desconto contratual', 'A fatura de outubro do nosso cliente maior saiu sem o desconto de 4% por volume previsto em contrato.', 'high', 'resolved', 37, 'sup1', 'Fatura gerada sem aplicar o desconto contratual', [
                ['s', 1.5, 'Boa tarde! Pode nos dizer o nome do cliente e o número da fatura?'],
                ['c', 3, 'Cliente Móveis Reunidos, fatura 2024-1873.'],
                ['n', 4, 'Desconto por volume soma as filiais, mas o cálculo só olha a filial da fatura. Bug confirmado, abrindo tarefa.'],
                ['s', 4.5, 'O desconto por volume não considera as filiais agrupadas na mesma fatura. Já temos a correção em andamento e vamos regenerar a fatura de vocês.'],
                ['s', 200, 'Correção liberada e a fatura 2024-1873 foi regenerada com o desconto de 4%.'],
                ['c', 202, 'Recebida e conferida. Obrigado!'],
            ]],
            [4, 'Integração com Protheus parou de enviar títulos', 'Desde a madrugada os títulos de frete não chegam ao contas a receber do Protheus.', 'urgent', 'resolved', 29, 'sup1', null, [
                ['s', 0.3, 'Bom dia! Já estamos verificando a integração.'],
                ['n', 0.6, 'Fila parada: serviço do conector caiu por falta de memória no worker. Reiniciei e reprocessei a fila.'],
                ['s', 0.8, 'O conector com o Protheus estava parado por um problema de memória. Reiniciamos e reprocessamos os 214 títulos pendentes. Pode confirmar se já chegaram?'],
                ['c', 1.4, 'Chegaram todos, obrigado!'],
                ['s', 1.6, 'Ótimo! Também ajustamos o alerta para avisar o plantão antes que a fila acumule.'],
            ]],
            [5, 'Painel de entregas não carrega após a atualização', 'Depois da atualização de ontem o painel de entregas fica em branco para o usuário master.', 'high', 'resolved', 17, 'sup1', 'Painel de entregas não carrega após a atualização', [
                ['s', 0.5, 'Bom dia! Poderia enviar um print do console do navegador (F12)?'],
                ['c', 1.2, 'Segue o print: aparece erro de tempo esgotado.'],
                ['n', 1.6, 'Contas com muitas filiais estouram o limite da consulta do painel. Hotfix agendado.'],
                ['s', 1.8, 'O painel estava estourando o tempo para contas com muitas filiais. Liberamos um ajuste e ele deve carregar normalmente. Pode confirmar?'],
                ['c', 5, 'Voltou ao normal. Obrigado!'],
            ]],
            [5, 'Solicitação de relatório de OTIF por cliente', 'Nossos embarcadores pedem mensalmente o OTIF (entrega no prazo e completa). Hoje montamos manualmente em planilha.', 'normal', 'open', 11, null, 'Relatório de OTIF (entrega no prazo e completa) por cliente', [
                ['s', 6, 'Olá! Obrigado pela sugestão. Já registramos a necessidade e vamos avaliar com o time de produto.'],
                ['n', 6.5, 'Terceiro cliente a pedir esse indicador. Entrou no backlog como feature, aguardando priorização.'],
            ]],
            [5, "Erro 'certificado expirado' ao emitir CT-e", 'Ao emitir CT-e aparece a mensagem de certificado digital expirado, mas o certificado foi renovado na semana passada.', 'urgent', 'resolved', 14, 'sup2', null, [
                ['s', 0.2, 'Olá! Vamos ajudar agora. O novo certificado já foi instalado no sistema ou apenas renovado na certificadora?'],
                ['c', 0.6, 'Só renovado na certificadora, acho que não instalamos.'],
                ['s', 0.9, 'Isso explica o erro. Acesse Configurações > Certificado digital, remova o antigo e envie o novo arquivo .pfx com a senha. Se preferir, fazemos junto por chamada de vídeo.'],
                ['c', 1.5, 'Pode ser por vídeo? Estou com receio de errar.'],
                ['s', 1.7, 'Claro! Entrei na sala que enviei por e-mail. Certificado atualizado e emissão normalizada.'],
                ['c', 2.4, 'Emitindo normalmente. Obrigado pela paciência!'],
            ]],
            [6, 'App do motorista não envia o comprovante de entrega (POD)', 'Os motoristas tiram a foto do canhoto mas o envio não conclui em áreas com sinal fraco e a entrega fica pendente.', 'high', 'in_progress', 6, 'sup1', 'Upload do comprovante de entrega (POD) falha com fotos grandes', [
                ['s', 1, 'Olá! Qual o modelo dos aparelhos dos motoristas e a versão do app?'],
                ['c', 5, 'Maioria Android, versão 4.2 do app.'],
                ['n', 6, 'Foto sem compressão passa de 8 MB. Em 3G dá timeout. Dev já está comprimindo e reenviando em partes.'],
                ['s', 7, 'O app envia a foto no tamanho original, o que falha com sinal fraco. Estamos preparando uma versão que comprime a imagem e reenvia em partes. Em teste interno agora.'],
                ['c', 30, 'Podem avisar quando liberarem? Vamos distribuir aos motoristas.'],
            ]],
            [6, 'Alteração de CNPJ do embarcador após o cadastro', 'Cadastramos um embarcador com o CNPJ errado e agora já existem CT-e emitidos. Como corrigir?', 'low', 'resolved', 25, 'sup2', null, [
                ['s', 2, 'Olá! Como já existem documentos fiscais emitidos, o CNPJ não pode ser alterado pelo sistema. O caminho correto é cadastrar o embarcador com o CNPJ certo e inativar o cadastro errado.'],
                ['c', 4, 'Entendi. Os CT-e antigos continuam válidos, certo?'],
                ['s', 4.5, 'Sim, os documentos já autorizados permanecem como emitidos. Os novos devem usar o cadastro correto.'],
            ]],
            [6, 'Roteirização sugere rota com restrição de caminhão', 'Em São Paulo o sistema sugere rotas dentro da zona de máxima restrição de circulação e nossos caminhões são multados.', 'normal', 'waiting_customer', 8, 'sup1', 'Considerar restrição de circulação de caminhões na roteirização', [
                ['s', 3, 'Boa tarde! Pode informar o tipo de veículo e o endereço de uma das entregas multadas?'],
                ['c', 18, 'VUC e carreta 3 eixos. Entrega na Rua Vergueiro, 2050.'],
                ['n', 20, 'Restrição de circulação (ZMRC) não está na base de roteirização. Virou tarefa de melhoria.'],
                ['s', 22, 'Confirmamos: hoje a roteirização não considera as áreas de restrição. Registramos uma melhoria. Vocês conseguem nos enviar o decreto ou a lista de ruas restritas usadas pela operação para acelerarmos?'],
            ]],
            [7, 'Não consigo emitir MDF-e: veículo sem RNTRC', "Ao emitir o MDF-e aparece 'RNTRC do veículo não informado'.", 'normal', 'resolved', 21, 'sup2', null, [
                ['s', 1, 'Olá! O RNTRC deve estar no cadastro do veículo ou da transportadora proprietária. Pode conferir em Cadastros > Veículos > aba ANTT?'],
                ['c', 3, 'Estava em branco mesmo. Cadastrei agora.'],
                ['c', 3.4, 'Emitiu! Obrigado.'],
            ]],
            [7, 'Cálculo de ICMS interestadual incorreto no CT-e', 'Nas operações para consumidor final em outro estado o valor do ICMS DIFAL está errado.', 'urgent', 'in_progress', 5, 'sup2', 'Corrigir o ICMS interestadual (DIFAL) no cálculo do CT-e', [
                ['s', 0.5, 'Olá! Pode informar a UF de origem e destino e o número de um CT-e com o valor incorreto?'],
                ['c', 1.5, 'Origem BA, destino SE. CT-e 4521.'],
                ['n', 2, 'Base do DIFAL usa o frete sem o ICMS embutido. Confirmado pelo contador da cliente. Crítico, já com o sênior.'],
                ['s', 2.4, 'Confirmamos o erro na base de cálculo do DIFAL. A correção está em teste interno e deve ser liberada em breve. Pedimos que evitem emitir novos CT-e dessa operação até lá.'],
                ['c', 3, 'Entendido, vamos segurar as emissões. Aguardo o aviso.'],
            ]],
            [7, 'Treinamento para a equipe de despacho', 'Contratamos três novos despachantes e gostaríamos de um treinamento sobre emissão de CT-e e MDF-e.', 'low', 'resolved', 31, 'sup1', null, [
                ['s', 3, 'Olá! Temos turmas online às quartas e sextas, com duração de duas horas. Quais dias funcionam melhor?'],
                ['c', 10, 'Quarta-feira à tarde.'],
                ['s', 11, 'Agendado para a próxima quarta às 14h, o link foi enviado ao e-mail do responsável.'],
                ['c', 140, 'Treinamento excelente, a equipe já está emitindo sozinha. Obrigado!'],
            ]],
            [8, 'Portal do cliente não mostra o rastreio das cargas', 'Nossos clientes entram no portal e a lista de cargas aparece vazia, mas as cargas existem no sistema.', 'high', 'resolved', 19, 'sup1', null, [
                ['s', 1, 'Boa tarde! Pode informar o CNPJ de um dos clientes e se ele já via as cargas antes?'],
                ['c', 3, 'Já via antes. Começou depois que alteramos o cadastro do cliente na semana passada.'],
                ['n', 4, 'Cadastro do cliente perdeu o vínculo com o embarcador ao trocar o CNPJ. Reativado o vínculo.'],
                ['s', 4.5, 'Ao alterar o cadastro, o vínculo do cliente com o embarcador foi desfeito. Refizemos o vínculo e as cargas já aparecem no portal.'],
                ['c', 6, 'Voltou. Obrigado!'],
            ]],
            [8, 'Manifesto de carga em PDF com a logomarca do cliente', 'Entregamos o manifesto ao embarcador e gostaríamos de incluir a logomarca no PDF.', 'low', 'open', 9, null, 'Manifesto de carga em PDF com a logomarca do cliente', [
                ['s', 20, 'Olá! Obrigada pela sugestão, já está registrada para avaliação do produto.'],
            ]],
            [9, 'Suporte ao modal fluvial no cadastro de veículos', 'Operamos com balsas no Rio Amazonas e hoje cadastramos como carreta, o que distorce os custos.', 'normal', 'open', 13, null, 'Suporte ao modal fluvial (balsas) no cadastro de veículos', [
                ['s', 5, 'Olá! Entendemos a necessidade. Registramos o pedido como melhoria e vamos levar ao planejamento do produto.'],
                ['n', 5.5, 'Segundo cliente do norte com a mesma necessidade. Priorizar junto com cálculo de frete fluvial.'],
            ]],
            [9, 'Horários de coleta com uma hora de diferença', 'As coletas agendadas em Manaus aparecem uma hora adiantadas no sistema.', 'normal', 'resolved', 34, 'sup1', 'Horários de coleta com uma hora de diferença no fuso do Amazonas', [
                ['s', 2, 'Olá! Pode nos dizer em que tela os horários aparecem com a diferença?'],
                ['c', 5, 'Na agenda de coletas e no comprovante enviado ao cliente.'],
                ['n', 6, 'Fuso do estado do AM ficou como Brasília. Cadastro da filial usa fuso global. Bug simples, vai para o júnior.'],
                ['s', 6.5, 'O sistema estava usando o horário de Brasília para a filial de Manaus. Já corrigimos e atualizamos os agendamentos existentes.'],
                ['c', 100, 'Horários corretos agora. Obrigado!'],
            ]],
            [10, 'Agendamento de docas: conflito de horário entre veículos', 'O sistema permitiu agendar dois caminhões na mesma doca e no mesmo horário.', 'high', 'resolved', 47, 'sup2', null, [
                ['s', 1, 'Olá! Pode enviar o número dos agendamentos conflitantes?'],
                ['c', 2.5, 'Agendamentos 3310 e 3312, doca 4, dia 14 às 10h.'],
                ['n', 3, 'Janela de duas horas por doca não era validada quando um dos agendamentos vinha por importação. Ajustei a regra.'],
                ['s', 3.2, 'A validação de conflito não era aplicada a agendamentos importados em lote. Ajustamos e remarcamos um dos veículos com a equipe de vocês.'],
                ['c', 5, 'Resolvido. Obrigado!'],
            ]],
            [10, 'Pedido de integração com o SAP via API', 'Nosso time de TI quer consumir os dados de entrega diretamente no SAP. Existe API disponível?', 'normal', 'in_progress', 12, 'sup2', null, [
                ['s', 4, 'Olá! Sim, temos API de rastreio e de eventos de entrega. Enviei a documentação e uma chave de acesso de homologação.'],
                ['c', 30, 'Recebemos, vamos testar. Existe limite de requisições?'],
                ['s', 31, 'Sim, hoje há um limite por minuto e estamos implementando a configuração por contrato. Para volumes altos recomendamos usar os eventos por webhook, que estão no nosso planejamento.'],
                ['c', 55, 'Faremos os testes com o limite atual e voltamos com o volume esperado.'],
            ]],
            [10, 'Relatório de custo por tonelada transportada', 'Precisamos de um relatório que mostre o custo médio por tonelada, por rota e por transportadora.', 'normal', 'waiting_customer', 6, 'sup2', null, [
                ['s', 2, 'Olá! Já temos o relatório de custo por rota e tonelada. Pode me confirmar se o recorte por transportadora também é obrigatório?'],
                ['c', 6, 'Sim, precisamos comparar transportadoras no mesmo trecho.'],
                ['s', 7, 'Perfeito. Podemos montar usando os filtros do relatório atual. Preciso saber o período e as rotas que quer comparar para preparar um modelo pronto.'],
            ]],
            [11, 'Cotação de frete retorna valor zerado para determinado destino', 'Para entregas em algumas cidades do Ceará a cotação mostra R$ 0,00 e o pedido segue sem valor.', 'urgent', 'resolved', 40, 'sup1', 'Cotação de frete retorna valor zerado para destinos com tabela por CEP', [
                ['s', 0.3, 'Bom dia! Já estamos olhando. Pode informar o CEP de destino de um dos casos?'],
                ['c', 0.9, 'CEP 63900-000 e 62010-000.'],
                ['n', 1.4, 'CEP cai entre duas faixas da tabela, sem faixa de fallback. Cotação retorna zero em vez de erro. Crítico.'],
                ['s', 1.6, 'Identificamos: o CEP cai entre duas faixas da tabela por região e a cotação retornava zero. Já escalamos como crítico.'],
                ['s', 26, 'Correção em produção: agora o sistema usa a faixa mais próxima e alerta quando falta cobertura na tabela. Os pedidos zerados podem ser recotados.'],
                ['c', 28, 'Recotados e valores certos. Muito obrigado!'],
            ]],
            [11, 'Etiqueta de volume com código de barras ilegível', 'O código de barras das etiquetas está sendo impresso muito fino e o leitor não lê no CD.', 'normal', 'resolved', 26, 'sup2', null, [
                ['s', 2, 'Olá! Qual impressora e resolução vocês usam?'],
                ['c', 5, 'Zebra ZT230, 203 dpi.'],
                ['s', 6, 'Aumentamos a largura mínima das barras no modelo de etiqueta para 203 dpi. Pode reimprimir uma etiqueta para testar?'],
                ['c', 8, 'Leu direto no leitor. Obrigado!'],
            ]],
            [12, 'Pesagem na balança não atualiza o ticket de entrada', 'O peso da balança rodoviária não chega ao ticket de entrada e o operador digita manualmente.', 'high', 'open', 3, null, null, [
                ['c', 26, 'Hoje o operador digitou o peso à mão em 14 caminhões e a fila na portaria está crescendo. Alguém consegue olhar?'],
            ]],
            [2, 'Sistema indisponível para a filial de Goiânia', 'Ninguém da filial Goiânia consegue entrar no sistema desde as 8h. Mensagem de erro de conexão.', 'urgent', 'open', 0.08, null, null, [
                ['c', 0.05, 'Já tentamos em dois computadores e pelo celular, o erro é o mesmo.'],
            ]],
            [11, 'Erro ao emitir MDF-e com mais de 3 documentos vinculados', 'Ao vincular quatro CT-e no mesmo manifesto o botão Emitir não responde.', 'high', 'open', 0.7, null, null, [
                ['c', 3, 'Testamos em dois navegadores. Com três CT-e emite normalmente.'],
            ]],
            [12, 'Cadastro de fazenda com múltiplas inscrições estaduais', 'Uma de nossas fazendas tem inscrições em dois estados e o cadastro só aceita uma.', 'normal', 'resolved', 38, 'sup2', null, [
                ['s', 3, 'Olá! Para o seu caso cadastre uma unidade para cada inscrição estadual, ambas vinculadas ao mesmo CNPJ. Assim o sistema usa a IE correta na emissão fiscal.'],
                ['c', 8, 'Fiz assim e deu certo. Obrigado!'],
            ]],
            [13, 'Reentrega: como registrar ocorrência de cliente ausente?', 'Quando o destinatário está ausente, como registramos a ocorrência e programamos nova tentativa?', 'low', 'resolved', 30, 'sup1', null, [
                ['s', 2, 'Olá! Na entrega, escolha "Registrar ocorrência" e selecione "Destinatário ausente". O sistema gera automaticamente uma nova tentativa para o próximo dia útil.'],
                ['c', 3, 'Achei! Obrigada.'],
            ]],
            [13, 'Fatura enviada em duplicidade por e-mail', 'Nossos clientes receberam a mesma fatura duas vezes por e-mail.', 'normal', 'resolved', 23, 'sup2', null, [
                ['s', 2, 'Olá! Pode informar o número da fatura e os destinatários?'],
                ['c', 3, 'Fatura 2024-2210. Dois destinatários receberam duas vezes.'],
                ['n', 4, 'Cliente cadastrado com o mesmo e-mail no financeiro e no comercial. Sistema envia para cada contato sem remover duplicados.'],
                ['s', 4.5, 'O cliente tinha o mesmo e-mail em dois contatos e o sistema enviava para cada um. Removemos a duplicidade no cadastro e vamos tratar o envio para deduplicar endereços.'],
            ]],
            [14, 'Peso cubado diferente na cotação e no CT-e', 'A cotação mostra 1.250 kg cubados e o CT-e sai com 980 kg. O cliente está contestando.', 'high', 'in_progress', 3, 'sup2', 'Peso cubado divergente entre cotação e CT-e', [
                ['s', 1, 'Olá! Pode enviar a cotação e o CT-e para compararmos e o fator de cubagem do contrato?'],
                ['c', 4, 'Enviei por e-mail. O contrato define 300 kg/m³.'],
                ['n', 5, 'Cotação lê o fator do contrato, emissão usa o padrão da transportadora (167). Confirmado, dev em andamento.'],
                ['s', 5.5, 'A cotação usa o fator do contrato, mas a emissão usa o padrão da transportadora. Isso será corrigido para usar o fator do contrato nos dois pontos. A correção está em andamento.'],
            ]],
            [15, 'Alerta de temperatura não dispara quando o sensor perde sinal', 'Um baú refrigerado ficou 40 minutos sem sinal e ninguém foi avisado. A carga era de laticínios.', 'high', 'in_progress', 6, 'sup1', 'Alertar quando o sensor de temperatura perde o sinal', [
                ['s', 0.8, 'Olá! Entendemos a gravidade. Pode informar a placa do veículo e o horário aproximado?'],
                ['c', 2, 'Placa QWE4A55, entre 14h10 e 14h50.'],
                ['n', 3, 'Alerta só cobre temperatura fora da faixa. Sem sinal não gera evento. Hotfix em revisão.'],
                ['s', 3.5, 'Hoje o alerta dispara só quando a temperatura sai da faixa; falta alertar a perda de sinal. Já temos a correção em revisão e queremos configurar o tempo sem sinal por cliente. Qual tempo vocês consideram aceitável?'],
                ['c', 5, 'Dez minutos já seria bom. E se puder avisar por e-mail também.'],
            ]],
            [15, 'Usuário somente leitura para auditoria', 'A auditoria externa precisa consultar documentos e relatórios sem poder alterar nada.', 'low', 'resolved', 20, 'sup1', null, [
                ['s', 2, 'Olá! Temos o perfil Auditoria, com acesso somente de consulta. Posso criar o acesso se enviar nome e e-mail do auditor.'],
                ['c', 6, 'Carlos Henrique, carlos.auditoria@campobelo.test.'],
                ['s', 7, 'Acesso criado com validade de 30 dias, que pode ser renovada.'],
            ]],
            [16, 'Fila de importação de NF-e travada', 'Mais de 800 notas estão há uma hora na fila de importação e não processam.', 'urgent', 'resolved', 12, 'sup2', null, [
                ['s', 0.2, 'Bom dia! Vamos verificar imediatamente.'],
                ['n', 0.5, 'Worker de importação travado em um XML malformado, o restante da fila ficou bloqueado atrás dele. Removi o XML e reprocessei.'],
                ['s', 0.7, 'Um XML corrompido bloqueava a fila. Retiramos o arquivo e a fila já está sendo processada normalmente. O XML com problema foi enviado ao seu e-mail para reenvio pelo emissor.'],
                ['c', 1.3, 'Fila zerada, obrigado!'],
            ]],
            [16, 'Painel de SLA de entrega por transportadora', 'Queremos acompanhar o percentual de entregas no prazo por transportadora num único painel.', 'normal', 'open', 7, null, 'Painel de SLA de entrega por transportadora', [
                ['s', 8, 'Olá! Boa sugestão. Já estamos avaliando junto com o indicador de OTIF, pois compartilham os mesmos dados.'],
            ]],
            [16, 'API de rastreio retorna 429 em horário de pico', 'Nossa integração recebe respostas 429 entre 8h e 9h e perdemos atualizações de status.', 'high', 'in_progress', 4, 'sup2', 'Limite de requisições por cliente na API de rastreio', [
                ['s', 1, 'Olá! Pode nos dizer quantas requisições por minuto vocês fazem nesse horário?'],
                ['c', 3, 'Cerca de 600 por minuto, consultando cada carga individualmente.'],
                ['n', 4, 'Limite atual global é 300/min por cliente. Dev já fazendo limite configurável por contrato e janela deslizante.'],
                ['s', 4.5, 'O limite atual é de 300 requisições por minuto. Estamos entregando limite por contrato e recomendamos consultar em lote ou usar webhooks. Podemos ajustar o limite do contrato de vocês assim que a versão for liberada.'],
            ]],
            [17, 'Divergência de estoque entre o WMS e o TMS na expedição', 'Volumes expedidos no WMS aparecem a menos no TMS.', 'high', 'resolved', 52, 'sup1', null, [
                ['s', 1, 'Olá! Pode listar as notas com divergência para conferirmos no log da integração?'],
                ['c', 6, 'NF 15032 e 15040. Foram expedidos 120 volumes e o TMS recebeu 100.'],
                ['n', 7, 'Volumes divididos em dois pallets e o WMS enviou a segunda mensagem após o fechamento do manifesto no TMS.'],
                ['s', 7.5, 'A segunda parte da expedição chegou depois do fechamento do manifesto. Ajustamos a integração para aguardar a confirmação do WMS antes de fechar e reprocessamos as notas.'],
                ['c', 12, 'Quantidades corretas agora. Obrigado!'],
            ]],
            [17, 'Autorização para emitir CT-e de redespacho', 'Preciso emitir CT-e de redespacho entre duas transportadoras parceiras. Como habilito?', 'normal', 'resolved', 18, 'sup2', null, [
                ['s', 2, 'Olá! O CT-e de redespacho é habilitado por transportadora, em Configurações > Fiscal. Ativei para as duas filiais que você mencionou. Qualquer dúvida na emissão, nos chame.'],
                ['c', 4, 'Funcionou, emiti o primeiro. Obrigado!'],
            ]],
            [18, 'Etiquetas de envio com endereço truncado', 'Endereços longos aparecem cortados nas etiquetas e os correios devolvem os pacotes.', 'normal', 'resolved', 28, 'sup2', 'Endereço truncado nas etiquetas de volume', [
                ['s', 2, 'Olá! Pode enviar uma etiqueta de exemplo?'],
                ['c', 4, 'Anexei o PDF. Perceba que o complemento não aparece.'],
                ['n', 5, 'Campo limitado a 60 caracteres no layout. Ajuste simples, vai para o júnior.'],
                ['s', 5.5, 'O endereço era limitado a 60 caracteres na etiqueta. Já corrigimos para quebrar em duas linhas e incluir o complemento.'],
                ['c', 80, 'Etiquetas saindo certas. Obrigado!'],
            ]],
            [18, 'Cálculo de frete para devoluções (logística reversa)', 'As devoluções hoje são lançadas como frete normal e precisamos corrigir o valor manualmente.', 'normal', 'open', 10, null, 'Cálculo de frete para devoluções (logística reversa)', [
                ['s', 12, 'Olá! Entendemos a dificuldade. Registramos a melhoria e vamos levar para o planejamento.'],
                ['c', 40, 'Podem nos dar uma previsão?'],
                ['s', 41, 'Ainda não há data definida. Assim que for priorizada, retornamos por este chamado.'],
            ]],
            [19, 'Entregador não consegue finalizar a entrega offline', 'Em prédios e regiões sem sinal o entregador não consegue concluir a entrega no app.', 'high', 'in_progress', 5, 'sup1', 'Finalizar entrega sem internet e sincronizar depois', [
                ['s', 1, 'Olá! Hoje o app exige conexão para concluir. Estamos desenhando o modo offline.'],
                ['c', 3, 'Isso afeta muito nossa operação em condomínios. Podemos ajudar a testar?'],
                ['n', 4, 'Cliente piloto para o modo offline. Combinar com o PO a lista mínima de funções.'],
                ['p', 28, 'Olá! Sou a Camila, do produto. Vamos levar o modo offline para o próximo ciclo e adoraríamos contar com vocês como piloto.'],
                ['c', 30, 'Combinado, contem conosco!'],
            ]],
            [20, 'Cobrança de frete por zona não aplica a tarifa mínima', 'Para entregas dentro da zona 1 o sistema cobra menos do que a tarifa mínima contratada.', 'normal', 'resolved', 16, 'sup1', null, [
                ['s', 2, 'Olá! Pode informar o cliente e o contrato? A tarifa mínima precisa estar marcada como obrigatória na tabela.'],
                ['c', 4, 'Contrato Mercado Central. Não sei se está marcada.'],
                ['s', 5, 'Estava desmarcada. Ajustei a tabela e recalculei as cotações em aberto. Pode conferir?'],
                ['c', 6, 'Conferi, agora respeita o mínimo. Obrigado!'],
            ]],
            [21, 'Rastreabilidade de lote de medicamentos no manifesto', 'A vigilância sanitária exige que o manifesto traga o lote dos medicamentos transportados.', 'high', 'waiting_customer', 5, 'sup2', null, [
                ['s', 2, 'Olá! Entendemos a exigência. Hoje o lote não aparece no manifesto. Para avaliarmos a alteração, pode nos enviar a norma ou o modelo de documento aceito?'],
                ['c', 20, 'Vou pedir ao responsável técnico e envio.'],
                ['s', 90, 'Olá! Algum retorno sobre o modelo de documento? Com ele conseguimos estimar o prazo.'],
            ]],
            [21, 'Erro ao assinar MDF-e com certificado A3', 'O token do certificado A3 não é reconhecido na assinatura do MDF-e e paramos a expedição.', 'urgent', 'resolved', 13, 'sup2', null, [
                ['s', 0.3, 'Bom dia! Vamos acompanhar agora. O driver do token está instalado na máquina que assina?'],
                ['c', 0.8, 'Reinstalamos o driver, mas continua o erro.'],
                ['n', 1.2, 'Falha na biblioteca de assinatura com token SafeNet em Windows 11. Cliente tem plano B com certificado em nuvem.'],
                ['s', 1.4, 'O problema é na leitura do token no Windows 11. Como alternativa imediata, podemos assinar com o certificado A1 em nuvem. Podemos configurar agora?'],
                ['c', 2, 'Pode sim, precisamos voltar a emitir.'],
                ['s', 3, 'Configurado e emitindo. Também abrimos uma melhoria para suportar certificado A3 em nuvem de forma nativa.'],
                ['c', 3.5, 'Perfeito, expedição liberada. Obrigado!'],
            ]],
            [22, 'Fatura consolidada mensal por centro de custo', 'Nosso financeiro quer uma fatura única por mês, aberta por centro de custo.', 'normal', 'open', 6, null, 'Fatura consolidada mensal por centro de custo', [
                ['s', 9, 'Olá! Obrigado pelo detalhamento. Vamos registrar como melhoria e avaliar com o time de faturamento.'],
            ]],
            [22, 'Frete por tonelada com arredondamento diferente do contrato', 'O contrato prevê arredondamento para cima em frações de 100 kg e o sistema arredonda para o mais próximo.', 'high', 'resolved', 24, 'sup2', null, [
                ['s', 2, 'Olá! Pode informar o cliente e a regra exata do contrato?'],
                ['c', 5, 'Cliente Metalúrgica Sul. Frações acima de 100 kg arredondam para cima.'],
                ['n', 6, 'Regra de arredondamento está parametrizada por contrato, mas o cadastro estava como "mais próximo". Ajuste de configuração.'],
                ['s', 6.5, 'O contrato estava configurado com arredondamento padrão. Ajustei para "sempre para cima" em frações de 100 kg e recalculei as cotações abertas.'],
                ['c', 8, 'Valores batem com o contrato. Obrigado!'],
            ]],
            [4, 'Lentidão geral no sistema às segundas de manhã', 'Toda segunda por volta das 8h o sistema fica lento para toda a operação.', 'normal', 'resolved', 20, 'sup1', null, [
                ['s', 1, 'Olá! Vamos acompanhar o próximo pico. Pode nos dizer se há alguma rotina agendada nesse horário, como importações ou relatórios?'],
                ['c', 5, 'Temos o fechamento semanal por volta das 8h.'],
                ['n', 6, 'Fechamento semanal concorre com importações em lote. Reorganizado o agendamento para 6h e ajustado índice.'],
                ['s', 6.5, 'A rotina de fechamento disputava recursos com as importações. Reprogramamos para 6h e otimizamos a consulta. Pode acompanhar na próxima segunda e nos avisar?'],
                ['c', 150, 'Segunda passada tudo normal. Obrigado!'],
            ]],
            [5, 'Solicitação de treinamento sobre conciliação de fretes', 'A equipe financeira quer aprender a usar a conciliação de fretes contratados e faturados.', 'low', 'resolved', 15, 'sup1', null, [
                ['s', 2, 'Olá! Podemos fazer uma sessão online de 1h com a equipe. Quais horários funcionam?'],
                ['c', 6, 'Quinta às 15h.'],
                ['s', 7, 'Agendado. O link foi enviado ao e-mail do financeiro.'],
                ['c', 100, 'Excelente! Já estamos conciliando sozinhos.'],
            ]],
            [1, 'Dúvida sobre cancelamento de CT-e fora do prazo', 'Um CT-e emitido há 10 dias foi cancelado pelo cliente. É possível cancelar?', 'normal', 'resolved', 10, 'sup2', null, [
                ['s', 2, 'Olá! O prazo de cancelamento é de 168 horas após a autorização, desde que não haja MDF-e vinculado em trânsito. Fora desse prazo é necessário emitir um CT-e de anulação ou complementar, conforme o caso fiscal. Recomendo validar com o contador de vocês.'],
                ['c', 4, 'Entendi. Vamos emitir o de anulação. Obrigado!'],
            ]],
            [12, 'Cobrança de pedágio no frete de grãos', 'O pedágio está sendo cobrado no frete mesmo para rotas em que o embarcador paga direto.', 'normal', 'open', 2, null, null, [
                ['c', 30, 'Complemento: acontece apenas nas rotas do contrato de safra.'],
            ]],
        ];
    }
}
