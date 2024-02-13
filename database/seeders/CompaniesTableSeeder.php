<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Doradal'],
            ['name' => 'vvc.centro'],
            ['name' => 'VupCentro'],
            ['name' => 'Pastos'],
            ['name' => 'Mountland'],
            ['name' => 'Caucasia'],
            ['name' => 'palmaditas'],
            ['name' => 'VupGaleria'],
            ['name' => 'Vvctres'],
            ['name' => 'aksia'],
            ['name' => 'FENIX PASTO'],
        ];
        foreach ($data as $item) {
            Company::create($item);
        }
    }
}
