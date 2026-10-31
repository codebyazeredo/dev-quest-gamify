<?php

namespace Database\Seeders\Data;

/**
 * Cenário de demonstração: a RotaMax Sistemas mantém um TMS (sistema de gestão de transporte)
 * usado por transportadoras, embarcadores e operadores logísticos.
 *
 * Atores: po = Camila (Product Owner), sup1 = Mariana e sup2 = Diego (suporte), qa = Patrícia (testes),
 * rafael = desenvolvedor sênior, lucas = pleno, thiago = júnior.
 */
class TmsScenario
{
    /**
     * Cada tarefa: key, quadro (0 = TMS Core, 1 = Integrações), categoria, prioridade, criador, dev,
     * etapa, dias desde a criação, título, descrição e opções (due = dias até o prazo).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function tasks(): array
    {
        $rows = [
            // Rafael (sênior): entregas estruturais, incidentes críticos e infraestrutura
            ['sefaz-cte-4', 1, 'Feature', 'Alta', 'po', 'rafael', 'done', 72, 'Atualizar a emissão de CT-e para o layout 4.00', 'Adequar XML, assinatura digital e validações à nota técnica vigente da SEFAZ, mantendo a emissão em contingência.'],
            ['backup-automatico', 1, 'Infrastructure', 'Crítica', 'po', 'rafael', 'done', 68, 'Backup automático do banco com restauração testada', 'Hoje não há rotina agendada em produção. Definir retenção de 30 dias e teste mensal de restauração.'],
            ['ci-cd-pipeline', 1, 'Infrastructure', 'Alta', 'po', 'rafael', 'done', 64, 'Pipeline de CI/CD com testes e deploy em homologação', 'Rodar testes e análise estática a cada pull request e publicar automaticamente em homologação.'],
            ['refactor-motor-frete', 0, 'Refactoring', 'Alta', 'po', 'rafael', 'done', 60, 'Refatorar o motor de cálculo de frete por faixa de peso e distância', 'As regras de tabela, taxas e pedágio estão misturadas. Separar em regras testáveis antes de novos contratos.'],
            ['integracao-protheus', 1, 'Feature', 'Alta', 'po', 'rafael', 'done', 56, 'Conector com o ERP Protheus para envio de títulos a receber', 'Enviar faturas fechadas ao contas a receber do Protheus, com reprocessamento em caso de falha.'],
            ['averbacao-atm', 1, 'Feature', 'Alta', 'po', 'rafael', 'done', 52, 'Averbação automática de seguro de carga na emissão do CT-e', 'Averbar o embarque na seguradora no momento da autorização e guardar o protocolo no documento.'],
            ['cache-tabelas-frete', 1, 'Infrastructure', 'Normal', 'po', 'rafael', 'done', 49, 'Cache em Redis das tabelas de frete mais consultadas', 'Reduzir a carga do banco nas cotações em horário de pico.'],
            ['monitoramento-sentry', 1, 'Infrastructure', 'Normal', 'po', 'rafael', 'done', 46, 'Monitoramento de erros e alertas em produção', 'Erros em produção só aparecem no log do servidor. Centralizar e alertar o plantão.'],
            ['ciot-geracao', 0, 'Bug', 'Crítica', 'sup1', 'rafael', 'done', 41, 'Corrigir a geração de CIOT para motoristas autônomos', 'A geração falha quando o motorista possui mais de um veículo cadastrado. Transportadoras ficam sem liberar viagens.'],
            ['cotacao-zerada', 0, 'Bug', 'Crítica', 'sup1', 'rafael', 'done', 38, 'Cotação de frete retorna valor zerado para destinos com tabela por CEP', 'Quando o CEP de destino cai entre duas faixas, a cotação devolve R$ 0,00 e o pedido segue sem valor.'],
            ['api-rastreio-v2', 1, 'Feature', 'Alta', 'po', 'rafael', 'done', 36, 'API de rastreio v2 com eventos de entrega', 'Disponibilizar posição, status e ocorrências da carga para os sistemas dos clientes.'],
            ['indices-performance', 0, 'Improvement', 'Alta', 'po', 'rafael', 'done', 30, 'Revisar índices das tabelas de CT-e e manifestos', 'Consultas de fechamento mensal passam de 40 segundos nos clientes maiores.'],
            ['doc-arquitetura', 0, 'Documentation', 'Baixa', 'po', 'rafael', 'done', 27, 'Documentar a arquitetura do TMS e os pontos de integração', 'Diagrama de módulos, fluxo de emissão fiscal e responsabilidades por serviço.'],
            ['motor-roteirizacao-v2', 0, 'Feature', 'Alta', 'po', 'rafael', 'done', 22, 'Roteirização com janelas de entrega e capacidade do veículo', 'Considerar horário de recebimento do cliente, peso máximo e ordem de paradas.'],
            ['regras-icms-estados', 0, 'Improvement', 'Alta', 'sup2', 'rafael', 'done', 18, 'Atualizar a tabela de alíquotas de ICMS por UF', 'Alíquotas interestaduais e substituição tributária desatualizadas em alguns estados.'],

            // Lucas (pleno): módulos de negócio e correções de clientes
            ['portal-rastreio-cliente', 0, 'Feature', 'Normal', 'po', 'lucas', 'done', 66, 'Portal do cliente com rastreio de cargas por nota fiscal', 'O embarcador consulta o andamento da carga sem precisar ligar para a transportadora.'],
            ['conciliacao-fretes', 0, 'Feature', 'Alta', 'po', 'lucas', 'done', 58, 'Conciliação de fretes contratados x faturados', 'Comparar o frete cotado com o cobrado pelo transportador e apontar divergências.'],
            ['relatorio-custo-rota', 0, 'Feature', 'Normal', 'po', 'lucas', 'done', 50, 'Relatório de custo por rota e por tonelada transportada', 'Visão gerencial de custo para negociação de contratos.'],
            ['agendamento-docas', 0, 'Feature', 'Normal', 'po', 'lucas', 'done', 44, 'Agendamento de docas com controle de janela por veículo', 'Evitar dois veículos na mesma doca e horário.'],
            ['import-xml-lote', 1, 'Bug', 'Alta', 'sup2', 'lucas', 'done', 40, 'Importação de XML de NF-e com muitos itens estoura o tempo limite', 'Notas com mais de 500 itens derrubam a importação em lote e travam a fila.'],
            ['fatura-desconto', 0, 'Bug', 'Alta', 'sup1', 'lucas', 'done', 35, 'Fatura gerada sem aplicar o desconto contratual', 'O desconto por volume mensal não é considerado quando a fatura reúne mais de uma filial.'],
            ['validacao-cnpj', 0, 'Bug', 'Normal', 'sup1', 'lucas', 'done', 31, 'Validar CNPJ e inscrição estadual no cadastro de embarcadores', 'O cadastro aceita documentos inválidos e a emissão fiscal falha depois.'],
            ['permissoes-perfis', 0, 'Improvement', 'Normal', 'po', 'lucas', 'done', 26, 'Perfis de acesso por filial e por módulo', 'Usuário da filial A não deve ver cargas da filial B.'],
            ['exportacao-xlsx', 0, 'Improvement', 'Baixa', 'sup1', 'lucas', 'done', 20, 'Exportar listagens para XLSX respeitando os filtros', 'Hoje a exportação ignora os filtros aplicados em tela.'],
            ['dashboard-entregas-fix', 0, 'Bug', 'Alta', 'sup1', 'lucas', 'done', 15, 'Painel de entregas não carrega após a atualização', 'Depois da versão anterior o painel fica em branco para contas com muitas filiais.'],

            // Thiago (júnior): correções pontuais, documentação e pequenas melhorias
            ['timezone-am', 0, 'Bug', 'Normal', 'sup1', 'thiago', 'done', 33, 'Horários de coleta com uma hora de diferença no fuso do Amazonas', 'Operações em Manaus aparecem com horário de Brasília nos agendamentos.'],
            ['etiqueta-endereco', 0, 'Bug', 'Normal', 'sup2', 'thiago', 'done', 24, 'Endereço truncado nas etiquetas de volume', 'Endereços longos perdem o complemento e o motorista entrega no lugar errado.'],
            ['mascara-telefone', 0, 'Bug', 'Baixa', 'sup1', 'thiago', 'done', 21, 'Máscara de telefone aceita números com dígitos a mais', 'Cadastro de motoristas permite telefone inválido.'],
            ['ajuste-impressao', 0, 'Bug', 'Normal', 'sup2', 'thiago', 'done', 17, 'Layout de impressão do romaneio corta a última coluna', 'Na impressão A4 a coluna de volumes fica fora da página.'],
            ['doc-fluxo-cobranca', 0, 'Documentation', 'Baixa', 'po', 'thiago', 'done', 14, 'Documentar o fluxo de faturamento e cobrança', 'Passo a passo da geração de fatura até a baixa do título.'],
            ['campo-obs-coleta', 0, 'Improvement', 'Baixa', 'po', 'thiago', 'done', 11, 'Campo de observação interna na ordem de coleta', 'Solicitado por clientes para registrar instruções que não vão para o motorista.'],
            ['filtro-motoristas', 0, 'Improvement', 'Normal', 'po', 'thiago', 'rejected', 9, 'Filtro de motoristas por situação da CNH', 'Listar motoristas com CNH vencida ou a vencer em 30 dias.', ['reason' => 'O filtro ignora CNH sem data de validade cadastrada e quebra a tela de motoristas importados.']],

            // Em andamento
            ['mdfe-encerramento', 1, 'Bug', 'Crítica', 'sup1', 'rafael', 'doing', 3, 'Encerrar o MDF-e automaticamente após a confirmação da última entrega', 'Manifestos ficam abertos e a transportadora recebe rejeição ao emitir o próximo.', ['due' => 1]],
            ['icms-difal', 0, 'Bug', 'Crítica', 'sup2', 'rafael', 'testing', 5, 'Corrigir o ICMS interestadual (DIFAL) no cálculo do CT-e', 'Operações para consumidor final em outra UF estão com o valor do diferencial de alíquota incorreto.', ['due' => 1]],
            ['alerta-sensor', 1, 'Bug', 'Alta', 'sup1', 'rafael', 'review', 6, 'Alertar quando o sensor de temperatura perde o sinal', 'Hoje o alerta só dispara com temperatura fora da faixa; sem sinal não avisa ninguém.', ['due' => 3]],
            ['rate-limit-api', 1, 'Improvement', 'Alta', 'po', 'rafael', 'doing', 4, 'Limite de requisições por cliente na API de rastreio', 'Picos de consulta de um único cliente degradam o serviço para os demais.', ['due' => 5]],
            ['cte-contingencia', 1, 'Feature', 'Alta', 'po', 'rafael', 'approved', 9, 'Emissão de CT-e em contingência (EPEC)', 'Permitir emitir mesmo com a SEFAZ indisponível e regularizar depois.', ['due' => 2]],
            ['perf-listagem-cargas', 0, 'Improvement', 'Normal', 'sup1', 'lucas', 'review', 7, 'Listagem de cargas lenta com filtro por período', 'Consultas de 30 dias levam mais de 20 segundos em clientes com muito volume.', ['due' => 2]],
            ['app-pod-upload', 1, 'Bug', 'Alta', 'sup1', 'lucas', 'testing', 6, 'Upload do comprovante de entrega (POD) falha com fotos grandes', 'No app do motorista a foto do canhoto não envia em rede 3G e a entrega fica pendente.', ['due' => 0]],
            ['peso-cubado', 0, 'Bug', 'Alta', 'sup2', 'lucas', 'doing', 3, 'Peso cubado divergente entre cotação e CT-e', 'O fator de cubagem do contrato não é aplicado na emissão, só na cotação.', ['due' => -1]],
            ['app-offline-sync', 1, 'Feature', 'Alta', 'sup1', 'lucas', 'todo', 2, 'Finalizar entrega sem internet e sincronizar depois', 'Entregadores em área sem sinal precisam concluir a entrega e enviar quando a rede voltar.', ['due' => 10]],
            ['pedagio-calculo', 0, 'Bug', 'Alta', 'sup1', 'thiago', 'doing', 2, 'Cálculo de pedágio divergente na roteirização', 'O valor estimado não considera eixos suspensos e fica abaixo do real.', ['due' => 2]],
            ['pdf-manifesto-logo', 0, 'Feature', 'Baixa', 'sup1', 'thiago', 'todo', 4, 'Manifesto de carga em PDF com a logomarca do cliente', 'Pedido de transportadoras que entregam o manifesto ao embarcador.', ['due' => 14]],
            ['mensagens-validacao', 0, 'Improvement', 'Baixa', 'po', 'thiago', 'review', 5, 'Mensagens de validação mais claras nos formulários de carga', 'Mensagens genéricas dificultam a correção pelo operador.', ['due' => 3]],

            // Backlog e próximos passos
            ['assinatura-a3-cloud', 1, 'Improvement', 'Alta', 'sup2', null, 'todo', 8, 'Assinatura de MDF-e com certificado A3 em nuvem', 'Clientes com token físico perdem a emissão quando o equipamento falha.', ['due' => 12]],
            ['roteirizacao-rodizio', 0, 'Improvement', 'Normal', 'sup1', null, 'todo', 6, 'Considerar restrição de circulação de caminhões na roteirização', 'Rotas em capitais ignoram o rodízio e as áreas restritas.', ['due' => 20]],
            ['relatorio-otif', 0, 'Feature', 'Normal', 'sup2', null, 'backlog', 12, 'Relatório de OTIF (entrega no prazo e completa) por cliente', 'Indicador pedido por vários embarcadores para avaliar transportadoras.'],
            ['modal-fluvial', 0, 'Feature', 'Normal', 'sup2', null, 'backlog', 10, 'Suporte ao modal fluvial (balsas) no cadastro de veículos', 'Operações na Amazônia usam balsas e hoje cadastram como carreta.'],
            ['logistica-reversa', 0, 'Feature', 'Normal', 'sup1', null, 'backlog', 9, 'Cálculo de frete para devoluções (logística reversa)', 'Devoluções hoje são lançadas como frete normal com ajuste manual.'],
            ['fatura-consolidada-cc', 0, 'Feature', 'Normal', 'sup2', null, 'backlog', 7, 'Fatura consolidada mensal por centro de custo', 'Embarcadores querem uma fatura com a abertura por centro de custo.'],
            ['painel-sla-entrega', 0, 'Feature', 'Normal', 'sup1', null, 'backlog', 5, 'Painel de SLA de entrega por transportadora', 'Acompanhar atrasos e ocorrências por transportadora contratada.'],
            ['webhook-eventos', 1, 'Feature', 'Alta', 'po', null, 'backlog', 5, 'Webhooks de eventos de entrega para sistemas dos clientes', 'Evitar consulta repetida na API: o cliente recebe o evento quando a carga muda de status.'],
            ['checklist-veiculo-app', 1, 'Feature', 'Normal', 'po', null, 'backlog', 4, 'Checklist de inspeção do veículo no app do motorista', 'Registrar condição do veículo antes da saída com fotos.'],
            ['migracao-mysql', 1, 'Infrastructure', 'Normal', 'po', null, 'backlog', 3, 'Planejar a atualização do banco para MySQL 8.4', 'Versão atual sai de suporte; planejar janela e testes de compatibilidade.'],
        ];

        return array_map(static fn (array $row) => [
            'key' => $row[0], 'board' => $row[1], 'category' => $row[2], 'priority' => $row[3], 'by' => $row[4], 'dev' => $row[5],
            'stage' => $row[6], 'start' => $row[7], 'title' => $row[8], 'description' => $row[9], ...($row[10] ?? []),
        ], $rows);
    }

    /**
     * Comentários nas tarefas: chave da tarefa, autor, horas depois da criação e texto.
     *
     * @return array<int, array{0: string, 1: string, 2: int, 3: string}>
     */
    public static function comments(): array
    {
        return [
            ['mdfe-encerramento', 'rafael', 6, 'Reproduzi em homologação: o evento de encerramento só é disparado quando a última parada tem baixa manual. Vou tratar também a baixa pelo app.'],
            ['mdfe-encerramento', 'sup1', 8, 'Dois clientes já abriram chamado. Se precisar de XML de exemplo, consigo com a Expresso Serra Azul.'],
            ['mdfe-encerramento', 'po', 20, 'Prioridade máxima. Priorizem antes da virada do mês, quando o volume de manifestos dobra.'],
            ['icms-difal', 'rafael', 10, 'A base de cálculo do DIFAL estava usando o valor do frete sem o ICMS embutido. Corrigido e coberto por teste.'],
            ['icms-difal', 'qa', 26, 'Validei os cenários SP→MG, SP→BA e RS→SC. Falta conferir operação com substituição tributária.'],
            ['icms-difal', 'po', 30, 'A Transportadora Boa Viagem aguarda para emitir os CT-e pendentes. Quando aprovar, libero a versão no mesmo dia.'],
            ['alerta-sensor', 'rafael', 12, 'Defini o tempo sem sinal como configurável por cliente, padrão de 15 minutos.'],
            ['alerta-sensor', 'sup1', 14, 'O pessoal da Laticínios Campo Belo pediu que o alerta saia também por e-mail para o responsável da frota.'],
            ['rate-limit-api', 'rafael', 8, 'Vou usar janela deslizante por chave de API. Limite inicial de 300 requisições por minuto, ajustável por contrato.'],
            ['perf-listagem-cargas', 'lucas', 16, 'O gargalo era a contagem total para a paginação. Troquei por paginação simples e criei índice composto por filial e data.'],
            ['perf-listagem-cargas', 'rafael', 22, 'Revisei. Só ajustaria o índice para incluir a situação da carga, que também entra no filtro padrão.'],
            ['app-pod-upload', 'lucas', 9, 'Comprimindo a imagem no aparelho antes do envio e reenviando em partes quando a rede cai.'],
            ['app-pod-upload', 'qa', 28, 'Testei em Android 11 e 13 com rede limitada. Passou em todos os cenários, mas a barra de progresso fica em 100% antes de confirmar o envio.'],
            ['app-pod-upload', 'lucas', 30, 'Ajustado para só mostrar concluído após a confirmação do servidor.'],
            ['peso-cubado', 'lucas', 5, 'O fator de cubagem é lido do contrato na cotação, mas a emissão usa o padrão da transportadora. Alinhando as duas pontas.'],
            ['peso-cubado', 'sup2', 12, 'Cerâmica Pedra Viva cobra 300 kg/m³ no contrato. Vou anexar o contrato ao chamado para vocês conferirem.'],
            ['pedagio-calculo', 'thiago', 4, 'Encontrei onde os eixos são calculados. Posso incluir o eixo suspenso como parâmetro do veículo?'],
            ['pedagio-calculo', 'rafael', 6, 'Pode, mas deixe a regra no motor de frete e não na tela. Qualquer dúvida me chama.'],
            ['pedagio-calculo', 'thiago', 7, 'Combinado, vou mover para o serviço de cálculo e cobrir com teste.'],
            ['mensagens-validacao', 'thiago', 10, 'Revisei as mensagens dos formulários de carga e coleta. Posso estender para os cadastros depois?'],
            ['mensagens-validacao', 'po', 12, 'Faça só cargas e coletas agora; cadastros entram em outro card.'],
            ['cte-contingencia', 'rafael', 20, 'Fluxo completo validado em homologação com a SEFAZ simulada. Falta o time de suporte revisar o texto das mensagens ao operador.'],
            ['cte-contingencia', 'sup2', 24, 'Texto revisado. Só trocaria "regularização pendente" por "enviar à SEFAZ quando voltar".'],
            ['app-offline-sync', 'lucas', 5, 'Primeira ideia: fila local com SQLite no app e sincronização por lote ao detectar rede.'],
            ['app-offline-sync', 'po', 7, 'Defini com a Entrega Ágil que o mínimo é concluir entrega, registrar ocorrência e anexar foto.'],
            ['assinatura-a3-cloud', 'sup2', 3, 'Três clientes perderam emissão no último mês por falha no token. Seria bom ter pelo menos um fornecedor homologado.'],
        ];
    }

    /**
     * Checklists das tarefas em andamento: chave da tarefa e itens [texto, concluído?].
     *
     * @return array<string, array<int, array{0: string, 1: bool}>>
     */
    public static function checklists(): array
    {
        return [
            'mdfe-encerramento' => [['Reproduzir o problema em homologação', true], ['Disparar evento de encerramento na baixa pelo app', true], ['Tratar manifesto com paradas parcialmente entregues', false], ['Testes automatizados do encerramento', false], ['Comunicar o suporte para orientar os clientes', false]],
            'icms-difal' => [['Corrigir base de cálculo do DIFAL', true], ['Cobrir cenários SP→MG, SP→BA e RS→SC', true], ['Validar substituição tributária', false]],
            'rate-limit-api' => [['Definir janela e limite padrão', true], ['Middleware por chave de API', false], ['Resposta 429 com tempo de espera', false], ['Documentar no portal de desenvolvedores', false]],
            'perf-listagem-cargas' => [['Medir consultas com dados reais', true], ['Trocar contagem total por paginação simples', true], ['Índice composto por filial, data e situação', true]],
            'app-pod-upload' => [['Comprimir imagem no aparelho', true], ['Reenvio em partes', true], ['Confirmação do servidor antes de concluir', true], ['Testar em Android 11 e 13', true]],
            'pedagio-calculo' => [['Parametrizar eixos suspensos no veículo', false], ['Mover regra para o motor de frete', false], ['Testes com rotas reais', false]],
            'cte-contingencia' => [['Fluxo de emissão em EPEC', true], ['Fila de regularização posterior', true], ['Texto das mensagens revisado pelo suporte', true]],
        ];
    }
}
