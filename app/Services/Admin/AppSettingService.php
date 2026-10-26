<?php

namespace App\Services\Admin;

use App\Enums\LogoSize;
use App\Models\AppSetting;
use App\Repositories\AppSettingRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AppSettingService
{
    public function __construct(private AppSettingRepository $settings) {}

    public function current(): AppSetting
    {
        return $this->settings->current();
    }

    public function update(array $data, ?UploadedFile $logo): AppSetting
    {
        $setting = $this->settings->current();

        $attributes = [
            'company_name' => $data['company_name'] ?? null,
            'logo_size' => LogoSize::from($data['logo_size']),
            'primary_color' => strtolower($data['primary_color']),
        ];

        if ($logo !== null) {
            $this->deleteLogo($setting);
            $attributes['logo_path'] = $logo->store('branding', 'public');
        }

        return $this->settings->update($setting, $attributes);
    }

    private function deleteLogo(AppSetting $setting): void
    {
        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }
    }
}
