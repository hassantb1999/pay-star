<?php

namespace Database\Factories;

use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class TransactionFactory extends Factory
{

    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'amount' => rand(2, 100000),
            'transaction_type' => rand(0, count(Transaction::getTransactionsTypes())),
            'type' => ['CREDIT', 'DEBIT'][rand(0, 1)],
            'status' => 'DONE',
        ];
    }
}
