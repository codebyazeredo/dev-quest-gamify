<?php

namespace Database\Seeders;

use App\Models\BusinessSector;
use App\Models\Client;
use App\Services\AddressService;
use Illuminate\Database\Seeder;

/**
 * Carteira de clientes do TMS: transportadoras, embarcadores (indústria e distribuição),
 * operadores logísticos e empresas de entrega. A ordem da lista é usada pelo TicketSeeder.
 */
class ClientSeeder extends Seeder
{
    private const TRANSPORT = 'Logística e Transporte';

    private const CLIENTS = [
        ['Translog Rodoviário Ltda', 'translog', self::TRANSPORT, ['80250-104', 'Rua Comendador Araújo', '482', 'Batel', 'Curitiba', 'PR']],
        ['Expresso Serra Azul Transportes', 'serraazul', self::TRANSPORT, ['95020-470', 'Rua Sinimbu', '1960', 'Centro', 'Caxias do Sul', 'RS']],
        ['Rodonorte Cargas S.A.', 'rodonorte', self::TRANSPORT, ['74115-030', 'Avenida T-4', '1195', 'Setor Bueno', 'Goiânia', 'GO']],
        ['TransCerrado Logística', 'transcerrado', self::TRANSPORT, ['38400-100', 'Avenida Afonso Pena', '2230', 'Centro', 'Uberlândia', 'MG']],
        ['Via Sul Transportes e Logística', 'viasul', self::TRANSPORT, ['89201-001', 'Rua Blumenau', '350', 'Centro', 'Joinville', 'SC']],
        ['Frota Brasil Transportes', 'frotabrasil', self::TRANSPORT, ['13015-904', 'Avenida Francisco Glicério', '1100', 'Centro', 'Campinas', 'SP']],
        ['Cargo Nordeste Express', 'cargonordeste', self::TRANSPORT, ['51021-040', 'Avenida Engenheiro Domingos Ferreira', '2589', 'Boa Viagem', 'Recife', 'PE']],
        ['Transportadora Boa Viagem', 'boaviagem', self::TRANSPORT, ['41820-021', 'Avenida Tancredo Neves', '1632', 'Caminho das Árvores', 'Salvador', 'BA']],
        ['Rápido Pantanal Transportes', 'pantanal', self::TRANSPORT, ['79002-970', 'Avenida Afonso Pena', '2700', 'Centro', 'Campo Grande', 'MS']],
        ['Amazônia Fluvial Logística', 'amazoniafluvial', self::TRANSPORT, ['69020-030', 'Avenida Eduardo Ribeiro', '520', 'Centro', 'Manaus', 'AM']],
        ['Bebidas Vale Verde Indústria', 'valeverde', 'Indústria', ['14020-260', 'Avenida Presidente Vargas', '2121', 'Jardim América', 'Ribeirão Preto', 'SP']],
        ['Móveis Nobre Indústria', 'moveisnobre', 'Indústria', ['95700-000', 'Rua Marechal Deodoro', '705', 'Centro', 'Bento Gonçalves', 'RS']],
        ['Agro Safra Grãos S.A.', 'agrosafra', 'Agronegócio', ['78005-300', 'Avenida Getúlio Vargas', '1300', 'Araés', 'Cuiabá', 'MT']],
        ['Distribuidora Sol Nascente Alimentos', 'solnascente', 'Alimentação', ['60115-221', 'Avenida Desembargador Moreira', '2120', 'Aldeota', 'Fortaleza', 'CE']],
        ['Cerâmica Pedra Viva', 'pedraviva', 'Indústria', ['88801-500', 'Rua Marechal Deodoro', '215', 'Centro', 'Criciúma', 'SC']],
        ['Laticínios Campo Belo', 'campobelo', 'Alimentação', ['36010-011', 'Avenida Barão do Rio Branco', '2540', 'Centro', 'Juiz de Fora', 'MG']],
        ['LogiHub Operações Logísticas', 'logihub', self::TRANSPORT, ['07190-100', 'Rodovia Presidente Dutra', '1500', 'Cumbica', 'Guarulhos', 'SP']],
        ['Armazéns Gerais Porto Seco', 'portoseco', self::TRANSPORT, ['88301-100', 'Rua Hercílio Luz', '940', 'Centro', 'Itajaí', 'SC']],
        ['Fulfillment Express Brasil', 'fulfillmentexpress', 'Varejo e Comércio', ['06454-000', 'Alameda Rio Negro', '503', 'Alphaville', 'Barueri', 'SP']],
        ['Entrega Ágil Courier', 'entregaagil', self::TRANSPORT, ['30140-071', 'Avenida do Contorno', '4520', 'Funcionários', 'Belo Horizonte', 'MG']],
        ['Moto Rápido Entregas', 'motorapido', self::TRANSPORT, ['01310-200', 'Alameda Santos', '1400', 'Cerqueira César', 'São Paulo', 'SP']],
        ['Vida Farma Distribuidora de Medicamentos', 'vidafarma', 'Saúde', ['74210-010', 'Avenida 85', '1200', 'Setor Marista', 'Goiânia', 'GO']],
        ['Aço Forte Distribuição Siderúrgica', 'acoforte', 'Indústria', ['27255-250', 'Avenida Paulo de Frontin', '480', 'Aterrado', 'Volta Redonda', 'RJ']],
    ];

    public function run(): void
    {
        $addresses = app(AddressService::class);

        foreach (self::CLIENTS as $index => [$name, $domain, $sector, [$cep, $street, $number, $district, $city, $state]]) {
            Client::updateOrCreate(
                ['cnpj' => $this->validCnpj(12345000 + $index * 7919)],
                [
                    'razao_social' => $name,
                    'email' => "operacoes@{$domain}.test",
                    'is_active' => true,
                    'business_sector_id' => BusinessSector::where('name', $sector)->value('id'),
                    'address_id' => $addresses->resolve([
                        'cep' => $cep,
                        'logradouro' => $street,
                        'numero' => $number,
                        'bairro' => $district,
                        'cidade' => $city,
                        'estado' => $state,
                    ])->id,
                ],
            );
        }
    }

    /** CNPJ matriz (filial 0001) com dígitos verificadores calculados a partir da base de 8 dígitos. */
    private function validCnpj(int $root): string
    {
        $digits = str_pad((string) $root, 8, '0', STR_PAD_LEFT).'0001';

        foreach ([[5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2], [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]] as $weights) {
            $sum = 0;

            foreach ($weights as $position => $weight) {
                $sum += (int) $digits[$position] * $weight;
            }

            $remainder = $sum % 11;
            $digits .= (string) ($remainder < 2 ? 0 : 11 - $remainder);
        }

        return $digits;
    }
}
