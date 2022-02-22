<?php

namespace Tests\Unit;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function account_belongs_to_an_owner()
    {

        $account = Account::factory()->create();

        $this->assertInstanceOf(User::class, $account->owner);
    }


    /** @test */
    public function account_has_transfers()
    {

        $account = Account::factory()->create();

        $this->assertInstanceOf(Collection::class, $account->transfers);
    }

    /** @test */
    public function account_has_transactions()
    {

        $account = Account::factory()->create();

        $this->assertInstanceOf(Collection::class, $account->transactions);
    }

    /** @test */
    public function account_can_accept_adding_credit()
    {
        $account = Account::factory()->create(['credit' => 200]);

        $account->addCredit(200);

        $this->assertTrue($account->credit == 400);

    }


    /** @test */
    public function account_can_accept_adding_debit()
    {
        $account = Account::factory()->create(['credit' => 400]);

        $account->addDebit(200);

        $this->assertTrue($account->credit == 200);

    }
}
