<?php

namespace App\Http\Resources;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AppSetting */
class AppSettingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'company_name' => $this->company_name,
            'logo_url' => $this->logoUrl(),
            'logo_size' => $this->logo_size->value,
            'primary_color' => $this->primaryColor(),
        ];
    }
}
