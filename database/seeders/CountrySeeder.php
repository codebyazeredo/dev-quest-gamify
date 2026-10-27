<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    private const COUNTRIES = [
        'BR' => 'Brasil',
        'AR' => 'Argentina',
        'CL' => 'Chile',
        'CO' => 'Colômbia',
        'MX' => 'México',
        'PE' => 'Peru',
        'PT' => 'Portugal',
        'PY' => 'Paraguai',
        'UY' => 'Uruguai',
        'US' => 'Estados Unidos',
    ];

    public function run(): void
    {
        foreach (self::COUNTRIES as $iso2 => $name) {
            Country::updateOrCreate(['iso2' => $iso2], ['name' => $name]);
        }
    }
}
