<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AccountFactory extends Factory
{

    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'bank' => Bank::getBanks()[rand(0, 2)],
            'description' => $this->faker->name(),
            'credit' => rand(0, 100000),
            'account_number' => $this->faker->numerify('#############'),
            'shaba_number' => 'IR' . $this->faker->numerify('############'),

        ];
    }
}
