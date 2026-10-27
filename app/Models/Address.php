<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'country_id', 'cep', 'logradouro', 'numero', 'complemento', 'bairro',
    'cidade', 'estado', 'referencia', 'latitude', 'longitude',
])]
class Address extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    protected function formatted(): Attribute
    {
        return Attribute::get(function (): string {
            $street = implode(' - ', array_filter([
                $this->numero ? "{$this->logradouro}, {$this->numero}" : $this->logradouro,
                $this->complemento,
                $this->bairro,
            ]));

            return implode(', ', array_filter([$street, "{$this->cidade}/{$this->estado}", $this->cep, $this->country?->name]));
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
