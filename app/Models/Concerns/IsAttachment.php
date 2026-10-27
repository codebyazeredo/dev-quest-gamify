<?php

namespace App\Models\Concerns;

use App\Support\StoredFile;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait IsAttachment
{
    public const DISK = StoredFile::DISK;

    private const IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    private const SIZE_UNITS = ['B', 'KB', 'MB', 'GB'];

    public function isImage(): bool
    {
        return in_array($this->mime_type, self::IMAGE_MIME_TYPES, true);
    }

    public function canBeViewedInline(): bool
    {
        return $this->isImage() || $this->mime_type === 'application/pdf';
    }

    public function icon(): string
    {
        return match (true) {
            $this->isImage() => 'bi-file-earmark-image',
            $this->mime_type === 'application/pdf' => 'bi-file-earmark-pdf',
            str_contains($this->mime_type, 'zip') => 'bi-file-earmark-zip',
            str_contains($this->mime_type, 'spreadsheet'), str_contains($this->mime_type, 'excel'), str_contains($this->mime_type, 'csv') => 'bi-file-earmark-spreadsheet',
            str_contains($this->mime_type, 'word') => 'bi-file-earmark-word',
            str_contains($this->mime_type, 'presentation'), str_contains($this->mime_type, 'powerpoint') => 'bi-file-earmark-ppt',
            str_starts_with($this->mime_type, 'text/') => 'bi-file-earmark-text',
            default => 'bi-file-earmark',
        };
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(function (): string {
            $size = (float) $this->size;
            $unit = 0;

            while ($size >= 1024 && $unit < count(self::SIZE_UNITS) - 1) {
                $size /= 1024;
                $unit++;
            }

            return ($unit === 0 ? (string) (int) $size : number_format($size, 1, ',', '.')).' '.self::SIZE_UNITS[$unit];
        });
    }
}
