<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => $this->fakeValidCpf(),
            'rg' => null,
            'nascimento' => fake()->date(),
            'sexo' => fake()->randomElement(Gender::cases()),
            'email' => fake()->unique()->safeEmail(),
            'telefone1' => fake()->numerify('###########'),
            'telefone2' => null,
            'foto_path' => null,
        ];
    }

    private function fakeValidCpf(): string
    {
        $base = '';

        for ($i = 0; $i < 9; $i++) {
            $base .= (string) fake()->numberBetween(0, 9);
        }

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
