<?php

namespace App\Models;

use App\Enums\Gender;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nome
 * @property string $cpf
 * @property string|null $rg
 * @property Carbon $nascimento
 * @property Gender $sexo
 * @property string $email
 * @property string $telefone1
 * @property string|null $telefone2
 * @property string|null $foto_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['nome', 'cpf', 'rg', 'nascimento', 'sexo', 'email', 'telefone1', 'telefone2', 'foto_path'])]
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'nascimento' => 'date',
            'sexo' => Gender::class,
        ];
    }

    /**
     * @return HasOne<Address, $this>
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    /**
     * @return HasOne<User, $this>
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
