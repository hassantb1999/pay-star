<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Account>
 */
class AccountFactory extends Factory
{

    protected $model = Account::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {


        return [
            'owner_id' => User::all()->random()->id,
            'bank' => 'keshavarzi', //@todo: replace with Bank::getBanks()->random()
            'description' => $this->faker->name(),
            'account_number' => Str::random(10),
            'shaba_number' => 'IR' . Str::random(15),

        ];
    }
}
