<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Clinic;
use App\Models\Specialist;
use App\Models\User;
use Database\Factories\ClinicFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::factory(1)->create(["email"=>"pasien@gmail.com"]);


        User::factory(3)->create();

        //clinic
        Clinic::factory(3)->create();

        //specialist
        Specialist::create(['name' => 'Cardiologist']);
        Specialist::create(['name' => 'Dermatologist']);
        Specialist::create(['name' => 'General Practitioners']);
        Specialist::create(['name' => 'Obgyn']);
        Specialist::create(['name' => 'Eye']);


       //doctor
        User::factory(5)->create([
            'role' => 'doctor',
            'clinic_id'=> 1,
            'specialist_id' =>rand(1, 5),
        ]);

        User::factory(5)->create([
            'role' => 'doctor',
            'clinic_id'=> 2,
            'specialist_id' =>rand(1, 5),

        ]);


        User::factory(5)->create([
            'role' => 'doctor',
            'clinic_id'=> 3,
            'specialist_id' =>rand(1, 5),
        ]);



    }
}
