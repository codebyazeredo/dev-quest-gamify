<?php

namespace App\Repositories;

use App\Models\AppSetting;

class AppSettingRepository extends Repository
{
    protected function model(): string
    {
        return AppSetting::class;
    }

    public function current(): AppSetting
    {
        return AppSetting::current();
    }
}
