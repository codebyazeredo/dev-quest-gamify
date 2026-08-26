<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin', 'email' => 'admin@devquestgamify.test', 'role' => 'admin', 'cpfBase' => '111444777'],
            ['name' => 'Product Owner', 'email' => 'po@devquestgamify.test', 'role' => 'product_owner', 'cpfBase' => '222555888'],
            ['name' => 'Developer', 'email' => 'dev@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '333666999'],
            ['name' => 'Tester', 'email' => 'tester@devquestgamify.test', 'role' => 'tester', 'cpfBase' => '444777000'],
            ['name' => 'Suporte', 'email' => 'suporte@devquestgamify.test', 'role' => 'suporte', 'cpfBase' => '555888111'],
        ];

        foreach ($accounts as $account) {
            $person = Person::firstOrCreate(
                ['email' => $account['email']],
                [
                    'nome' => $account['name'],
                    'cpf' => $this->validCpf($account['cpfBase']),
                    'rg' => null,
                    'nascimento' => now()->subYears(30)->format('Y-m-d'),
                    'sexo' => 3,
                    'telefone1' => '00000000000',
                    'telefone2' => null,
                    'foto_path' => null,
                ]
            );

            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'person_id' => $person->id,
                ]
            );

            $user->syncRoles([$account['role']]);
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
