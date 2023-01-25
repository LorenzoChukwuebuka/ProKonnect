<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use App\Models\Referal_transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referal_transaction>
 */
class Referal_transactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Referal_transaction::class;
    public function definition()
    {
        return [
            "referred_by" => Arr::shuffle([1, 2, 3, 4, 5, 6, 7, 8, 9])[0],
            "user_referred" => Arr::shuffle([1, 2, 3, 4, 5, 6, 7, 8, 9])[0],
            "payment_id" => 0,
            "amount_earned" => Arr::shuffle([1, 2, 3, 4, 5, 6, 7, 8, 9])[0],
        ];
    }
}
