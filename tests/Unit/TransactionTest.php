<?php

namespace Tests\Unit;

use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Transaction;
use App\Modules\Banking\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function it_belongs_to_an_account()
    {

        $transaction = Transaction::factory()->create();

        $this->assertInstanceOf(Account::class, $transaction->account);
    }

    /** @test */
    public function it_can_create_a_pre_transaction()
    {
        $account = Account::factory()->create();
        $transaction = resolve(Transaction::class);
        $transaction->createPreTransaction($account->id, 1, Transfer::TRANSACTION_TYPE, Transaction::TYPE_DEBIT);

        $this->assertDatabaseHas('transactions', ['account_id' => $account->id]);
        $this->assertDatabaseMissing('transactions', ['status' => Transaction::STATUS_DONE]);
    }

    /** @test */
    public function it_can_approve_a_pre_transaction()
    {
        $account = Account::factory()->create();
        $transaction = resolve(Transaction::class);
        $transaction->createPreTransaction($account->id, 1, Transfer::TRANSACTION_TYPE, Transaction::TYPE_DEBIT);

        $transaction->approveTransaction();
        $this->assertDatabaseHas('transactions', ['account_id' => $account->id]);
        $this->assertDatabaseHas('transactions', ['status' => Transaction::STATUS_DONE]);

    }

    /** @test */
    public function approve_a_debit_pre_transaction_decrese_account_credit()
    {
        $account = Account::factory()->create(['credit' => 20]);
        $transaction = resolve(Transaction::class);
        $transaction->createPreTransaction($account->id, 1, Transfer::TRANSACTION_TYPE, Transaction::TYPE_DEBIT);

        $transaction->approveTransaction();
        $this->assertDatabaseHas('transactions', ['account_id' => $account->id]);
        $this->assertDatabaseHas('transactions', ['status' => Transaction::STATUS_DONE]);
        $this->assertDatabaseHas('accounts', ['credit' => 19]);
    }

    /** @test */
    public function approve_a_credit_pre_transaction_decrese_account_credit()
    {
        $account = Account::factory()->create(['credit' => 20]);
        $transaction = resolve(Transaction::class);
        $transaction->createPreTransaction($account->id, 1, Transfer::TRANSACTION_TYPE, Transaction::TYPE_CREDIT);

        $transaction->approveTransaction();
        $this->assertDatabaseHas('transactions', ['account_id' => $account->id]);
        $this->assertDatabaseHas('transactions', ['status' => Transaction::STATUS_DONE]);
        $this->assertDatabaseHas('accounts', ['credit' => 21]);
    }

}
