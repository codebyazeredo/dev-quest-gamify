<?php

namespace Database\Seeders;

use App\Models\BusinessSector;
use Illuminate\Database\Seeder;

class BusinessSectorSeeder extends Seeder
{
    private const SECTORS = [
        'Tecnologia da Informação',
        'Varejo e Comércio',
        'Saúde',
        'Educação',
        'Serviços Financeiros',
        'Indústria',
        'Logística e Transporte',
        'Alimentação',
        'Construção Civil',
        'Agronegócio',
        'Serviços Profissionais',
        'Energia',
        'Turismo e Hotelaria',
    ];

    public function run(): void
    {
        foreach (self::SECTORS as $name) {
            BusinessSector::firstOrCreate(['name' => $name]);
        }
    }
}
