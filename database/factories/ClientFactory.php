<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'razao_social' => fake()->company(),
            'cnpj' => $this->fakeValidCnpj(),
            'email' => fake()->unique()->companyEmail(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    private function fakeValidCnpj(): string
    {
        $cnpj = fake()->unique()->numerify('########').'0001';

        foreach ([[5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2], [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]] as $weights) {
            $sum = 0;

            foreach ($weights as $position => $weight) {
                $sum += (int) $cnpj[$position] * $weight;
            }

            $remainder = $sum % 11;
            $cnpj .= $remainder < 2 ? 0 : 11 - $remainder;
        }

        return $cnpj;
    }
}
