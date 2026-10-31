<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        AppSetting::current()->update(['company_name' => 'RotaMax Sistemas']);
    }
}
