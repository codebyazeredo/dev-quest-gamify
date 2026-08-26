<?php

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $person_id
 * @property string $cep
 * @property string $logradouro
 * @property string|null $numero
 * @property string $cidade
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['person_id', 'cep', 'logradouro', 'numero', 'cidade', 'estado'])]
class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
