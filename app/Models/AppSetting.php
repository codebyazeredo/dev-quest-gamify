<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $company_name
 * @property string|null $logo_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['company_name', 'logo_path'])]
class AppSetting extends Model
{
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
