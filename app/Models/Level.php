<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['level', 'xp_required'])]
class Level extends Model
{
    use HasFactory;

    /** Faixa visual (1 a 5): quanto mais alto o nível, mais elaborado o efeito do selo. */
    public static function tierOf(int $level): int
    {
        return match (true) {
            $level >= 35 => 5,
            $level >= 20 => 4,
            $level >= 10 => 3,
            $level >= 5 => 2,
            default => 1,
        };
    }
}
