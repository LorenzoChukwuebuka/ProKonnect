<?php

namespace Database\Factories;

use App\Models\Interests;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Interests>
 */
class InterestsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Interests::class;
    public function definition()
    {

        return [
         'interests'=>$this->faker->title
        ];
    }
}
