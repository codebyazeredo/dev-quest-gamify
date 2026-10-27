<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

#[Fillable(['nome', 'cpf', 'rg', 'nascimento', 'sexo', 'email', 'telefone1', 'telefone2', 'foto_path', 'address_id'])]
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

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->foto_path ? Storage::url($this->foto_path) : null);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
