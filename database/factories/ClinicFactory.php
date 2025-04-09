<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string<\App\Models\Clinic>
     */
    protected $model = Clinic::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->fakeClinicName(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'open_time' => fake()->time(),
            'close_time' => fake()->time(),
            'email' => fake()->unique()->safeEmail(),
            'website' => fake()->optional()->url(),
            'note' => fake()->optional()->paragraph(),
        ];
    }


    protected function fakeClinicName(): string
    {
        $prefixes = ['Med', 'Health', 'Care', 'Family', 'Community', 'Prime', 'Global', 'Elite', 'New', 'United'];
        $suffixes = ['Clinic', 'Center', 'Hospital', 'Medical', 'Group', 'Practice', 'Associates', 'Solutions', 'Institute', 'Wellness'];
        $specialties = ['General', 'Specialized', 'Dental', 'Vision', 'Mental Health', 'Physical Therapy', 'Pediatric', 'Cardiology', 'Dermatology', 'Neurology'];

        $chanceOfSpecialty = fake()->boolean(30); // 30% chance of including a specialty

        $name = fake()->randomElement($prefixes) . ' ' . fake()->randomElement($suffixes);

        if ($chanceOfSpecialty) {
            $name = fake()->randomElement($specialties) . ' ' . $name;
        }

        // Add a location sometimes
        if (fake()->boolean(20)) {
            $cities = ['South Tangerang', 'Jakarta', 'Depok', 'Bogor', 'Bekasi'];
            $name .= ' of ' . fake()->randomElement($cities);
        }

        return $name;
    }
}
