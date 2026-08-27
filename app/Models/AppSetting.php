<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['company_name', 'logo_path'])]
class AppSetting extends Model
{
    public const DEFAULT_NAME = 'Dev Quest - Gamify';

    public const DEFAULT_LOGO_URL = '/images/devquestlogo.png';

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function isConfigured(): bool
    {
        return $this->company_name !== null || $this->logo_path !== null;
    }

    public function displayName(): string
    {
        return $this->company_name ?: self::DEFAULT_NAME;
    }

    public function logoUrl(): ?string
    {
        if ($this->logo_path) {
            return Storage::url($this->logo_path);
        }

        return $this->isConfigured() ? null : self::DEFAULT_LOGO_URL;
    }
}
