<?php

namespace App\Models;

use App\Enums\LogoSize;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['company_name', 'logo_path', 'logo_size', 'primary_color'])]
class AppSetting extends Model
{
    public const DEFAULT_NAME = 'Dev Quest - Gamify';

    public const DEFAULT_LOGO_URL = '/images/devquestlogo.png';

    public const DEFAULT_PRIMARY_COLOR = '#0d6efd';

    public const PRIMARY_COLOR_PRESETS = [
        'Azul' => '#0d6efd',
        'Índigo' => '#6610f2',
        'Roxo' => '#6f42c1',
        'Rosa' => '#d63384',
        'Vermelho' => '#dc3545',
        'Laranja' => '#fd7e14',
        'Verde' => '#198754',
        'Turquesa' => '#20c997',
        'Ciano' => '#0aa2c0',
        'Grafite' => '#343a40',
    ];

    protected function casts(): array
    {
        return [
            'logo_size' => LogoSize::class,
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['logo_size' => LogoSize::LARGE]);
    }

    public function isConfigured(): bool
    {
        return $this->company_name !== null || $this->logo_path !== null;
    }

    public function displayName(): string
    {
        return $this->company_name ?: self::DEFAULT_NAME;
    }

    public function primaryColor(): string
    {
        return $this->primary_color ?: self::DEFAULT_PRIMARY_COLOR;
    }

    public function logoUrl(): ?string
    {
        if ($this->logo_path) {
            return Storage::url($this->logo_path);
        }

        return $this->isConfigured() ? null : self::DEFAULT_LOGO_URL;
    }
}
