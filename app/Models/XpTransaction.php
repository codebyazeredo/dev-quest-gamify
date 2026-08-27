<?php

namespace App\Models;

use App\Enums\XpSourceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'amount', 'source_type', 'source_id', 'description'])]
class XpTransaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'source_type' => XpSourceType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
