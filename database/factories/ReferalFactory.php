<?php

namespace Database\Factories;

use App\Models\Referal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referal>
 */
class ReferalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Referal::class;
    public function definition()
    {
        return [
            "referal_id" => Arr::shuffle([1, 2, 3, 4, 5, 6, 7, 8, 9])[0],
            "referee_id" => Arr::shuffle([1, 2, 3, 4, 5, 6, 7, 8, 9])[0],
        ];
    }
}
