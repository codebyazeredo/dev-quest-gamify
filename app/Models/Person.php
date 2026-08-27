<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['nome', 'cpf', 'rg', 'nascimento', 'sexo', 'email', 'telefone1', 'telefone2', 'foto_path'])]
class Person extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'nascimento' => 'date',
            'sexo' => Gender::class,
        ];
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
