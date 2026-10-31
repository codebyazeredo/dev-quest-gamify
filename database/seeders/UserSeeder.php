<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
use App\Services\AddressService;
use Illuminate\Database\Seeder;

/**
 * Equipe da RotaMax Sistemas, fabricante de um TMS (sistema de gestão de transporte).
 * As contas de demonstração continuam as mesmas do README: admin@, po@, dev@, tester@ e suporte@ (senha "password").
 */
class UserSeeder extends Seeder
{
    private const TEAM = [
        ['name' => 'Ricardo Almeida Prado', 'email' => 'admin@devquestgamify.test', 'role' => 'admin', 'cpfBase' => '111444777', 'sexo' => 1, 'city' => 0],
        ['name' => 'Camila Nogueira Reis', 'email' => 'po@devquestgamify.test', 'role' => 'product_owner', 'cpfBase' => '222555888', 'sexo' => 2, 'city' => 0],
        ['name' => 'Rafael Monteiro Costa', 'email' => 'rafael.monteiro@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '101202303', 'sexo' => 1, 'city' => 1],
        ['name' => 'Lucas Ferreira Lima', 'email' => 'dev@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '333666999', 'sexo' => 1, 'city' => 0],
        ['name' => 'Thiago Barros Santos', 'email' => 'thiago.barros@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '202303404', 'sexo' => 1, 'city' => 2],
        ['name' => 'Patrícia Duarte Moraes', 'email' => 'tester@devquestgamify.test', 'role' => 'tester', 'cpfBase' => '444777000', 'sexo' => 2, 'city' => 0],
        ['name' => 'Mariana Costa Souza', 'email' => 'suporte@devquestgamify.test', 'role' => 'suporte', 'cpfBase' => '555888111', 'sexo' => 2, 'city' => 1],
        ['name' => 'Diego Martins Rocha', 'email' => 'diego.martins@devquestgamify.test', 'role' => 'suporte', 'cpfBase' => '606707808', 'sexo' => 1, 'city' => 2],
    ];

    private const CITIES = [
        ['cidade' => 'Curitiba', 'estado' => 'PR', 'cep' => '80250-104', 'logradouro' => 'Rua Comendador Araújo', 'bairro' => 'Batel'],
        ['cidade' => 'São Paulo', 'estado' => 'SP', 'cep' => '04538-132', 'logradouro' => 'Avenida Brigadeiro Faria Lima', 'bairro' => 'Itaim Bibi'],
        ['cidade' => 'Joinville', 'estado' => 'SC', 'cep' => '89201-000', 'logradouro' => 'Rua do Príncipe', 'bairro' => 'Centro'],
    ];

    public function run(): void
    {
        $addresses = app(AddressService::class);

        foreach (self::TEAM as $index => $member) {
            $city = self::CITIES[$member['city']];

            $person = Person::firstOrCreate(
                ['email' => $member['email']],
                [
                    'address_id' => $addresses->resolve([...$city, 'numero' => (string) (120 + $index * 83)])->id,
                    'nome' => $member['name'],
                    'cpf' => $this->validCpf($member['cpfBase']),
                    'rg' => null,
                    'nascimento' => now()->subYears(24 + $index * 2)->subDays($index * 29)->format('Y-m-d'),
                    'sexo' => $member['sexo'],
                    'telefone1' => '4199'.str_pad((string) (1000000 + $index * 137), 7, '0', STR_PAD_LEFT),
                    'telefone2' => null,
                    'foto_path' => null,
                ]
            );

            $user = User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'person_id' => $person->id,
                ]
            );

            $user->syncRoles([$member['role']]);
        }
    }

    private function validCpf(string $base): string
    {
        $digits = $base;

        for ($round = 0; $round < 2; $round++) {
            $length = strlen($digits);
            $sum = 0;

            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $digits[$i] * (($length + 1) - $i);
            }

            $check = ($sum * 10) % 11;
            $digits .= (string) ($check === 10 ? 0 : $check);
        }

        return $digits;
    }
}
