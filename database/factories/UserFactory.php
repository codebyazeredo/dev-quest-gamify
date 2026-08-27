<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'person_id' => Person::factory(),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(UserRole::ADMIN->value));
    }

    public function productOwner(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(UserRole::PRODUCT_OWNER->value));
    }

    public function developer(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(UserRole::DEVELOPER->value));
    }

    public function tester(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(UserRole::TESTER->value));
    }

    public function suporte(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(UserRole::SUPORTE->value));
    }
}
