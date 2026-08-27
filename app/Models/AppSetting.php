<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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
    public const DEFAULT_NAME = 'Dev Quest - Gamify';

    public const DEFAULT_LOGO_URL = '/images/devquestlogo.png';

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    /**
     * Whether a company has customized branding (name and/or logo) at least once.
     */
    public function isConfigured(): bool
    {
        return $this->company_name !== null || $this->logo_path !== null;
    }

    public function displayName(): string
    {
        return $this->company_name ?: self::DEFAULT_NAME;
    }

    /**
     * The logo to display: the company's uploaded logo, or the system default
     * logo while no company has configured branding yet, or null once a
     * company has configured branding without setting a logo.
     */
    public function logoUrl(): ?string
    {
        if ($this->logo_path) {
            return Storage::url($this->logo_path);
        }

        return $this->isConfigured() ? null : self::DEFAULT_LOGO_URL;
    }
}
