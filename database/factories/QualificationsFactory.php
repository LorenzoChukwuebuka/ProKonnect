<?php

namespace Database\Factories;

use App\Models\Qualifications;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Qualifications>
 */
class QualificationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Qualifications::class;
     public function definition()
    {
        return [
            'qualification'=>$this->faker->title
        ];
    }
}
