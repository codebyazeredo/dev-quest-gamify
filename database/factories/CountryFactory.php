<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            // O prefixo Z evita colidir com os países do seeder e da migration (BR etc.).
            'iso2' => strtoupper(fake()->unique()->lexify('Z?')),
        ];
    }
}
