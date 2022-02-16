<?php

namespace Database\Seeders;

use App\Modules\Banking\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
//         \App\Models\User::factory(10)->create();
        Account::factory(3)->create(['owner_id' => 1]);
    }
}
