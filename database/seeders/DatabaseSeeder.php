<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Referal;
use App\Models\Service;
use App\Models\Interests;
use App\Models\University;
use App\Models\Qualifications;
use App\Models\Specialization;
use Illuminate\Database\Seeder;
use App\Models\Referal_transaction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Interests::factory(10)->create();
        // Qualifications::factory(10)->create();
        // Specialization::factory(10)->create();
        // University::factory(10)->create();
        // Service::factory(10)->create();
        Referal::factory(20)->create();
        Referal_transaction::factory(20)->create();


    }
}
