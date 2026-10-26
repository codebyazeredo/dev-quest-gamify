<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AppSettingRequest;
use App\Http\Resources\AppSettingResource;
use App\Models\User;
use App\Services\Admin\AppSettingService;
use Illuminate\Http\JsonResponse;

class AppSettingController extends Controller
{
    public function __construct(private AppSettingService $settings) {}

    public function update(AppSettingRequest $request): JsonResponse
    {
        $this->authorize('accessAdminPanel', User::class);

        $setting = $this->settings->update($request->safe()->except('logo'), $request->file('logo'));

        return AppSettingResource::make($setting)
            ->additional(['message' => 'Configurações salvas. As alterações foram aplicadas.'])
            ->response();
    }
}
