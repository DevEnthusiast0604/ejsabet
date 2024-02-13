<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        Setting::create(['key' => 'site_status', 'value' => 'active']); // active: all are active, inactive: only audit domain is inactive, disabled: all domains are inactive
        Setting::create(['key' => 'site_disable_time', 'value' => '[]']);
    }
}
