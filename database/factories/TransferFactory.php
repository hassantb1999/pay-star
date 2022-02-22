<?php

namespace Database\Factories;

use App\Modules\Banking\Models\Transaction;
use App\Modules\Banking\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class TransferFactory extends Factory
{

    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'transaction_id' => Transaction::factory(),
            'track_id' => Transfer::generateTransferTrackId(),
            'description' => $this->faker->text(20),
            'destinationFirstname' => $this->faker->name(),
            'destinationLastname' => $this->faker->name(),
            'destinationNumber' => $this->faker->numerify('#############'),
            'inquiryDate' => $this->faker->numerify('#####'),
            'inquiryTime' => $this->faker->text(20),
            'inquirySequence' => rand(1000, 1020),
            'message' => $this->faker->text(20),
            'paymentNumber' => $this->faker->numerify('#######'),
            'refCode' => $this->faker->numerify('#################'),
            'type' => ['internal;', 'paya'][rand(0, 1)],
        ];
    }
}
