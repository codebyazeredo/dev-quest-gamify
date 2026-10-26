<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LogoSize;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use App\Models\User;
use App\Services\Admin\AppSettingService;
use Illuminate\Contracts\View\View;

class AppSettingPageController extends Controller
{
    public function __construct(private AppSettingService $settings) {}

    public function index(): View
    {
        $this->authorize('accessAdminPanel', User::class);

        return view('admin.settings', [
            'setting' => AppSettingResource::make($this->settings->current())->resolve(),
            'colorPresets' => AppSetting::PRIMARY_COLOR_PRESETS,
            'logoSizes' => LogoSize::cases(),
        ]);
    }
}
