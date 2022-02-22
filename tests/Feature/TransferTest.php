<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Bank;
use App\Modules\Banking\Models\Transaction;
use App\Modules\Banking\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function guess_users_can_not_access_transfer_controller()
    {
        $transfer = Transfer::factory()->create();
        $this->get(Route('transfer.index'))->assertStatus(401);
        $this->get(Route('transfer.show', $transfer))->assertStatus(401);

        $transferdata = Transfer::factory()->raw();
        $this->post(Route('transfer.store', $transfer->transaction->account_id), $transferdata)->assertStatus(401);
    }

    /**
     * @test
     */
    public function users_can_not_see_others_transferes()
    {
        $first_user = User::factory()->create();
        $account = Account::factory()->create(['owner_id' => $first_user->id]);
        $second_user = User::factory()->create();
        Account::factory()->create(['owner_id' => $second_user->id]);

        Transfer::factory()->create([
            'transaction_id' => Transaction::factory()->create([
                'account_id' => $account->id,
                'transaction_type' => 1
            ])->id
        ]);


        $this->actingAs($second_user, 'sanctum');
        $this->get(Route('transfer.index'))->assertStatus(200)->assertExactJson([]);


        $this->actingAs($first_user, 'sanctum');
        $this->get(Route('transfer.index'))->assertStatus(200)->assertJsonStructure([
            []
        ]);

    }

    /**
     * @test
     */
    public function users_can_not_see_others_account_transfers()
    {
        $first_user = User::factory()->create();
        $account = Account::factory()->create(['owner_id' => $first_user->id]);
        $second_user = User::factory()->create();
        Account::factory()->create(['owner_id' => $second_user->id]);

        Transfer::factory()->create([
            'transaction_id' => Transaction::factory()->create([
                'account_id' => $account->id,
                'transaction_type' => 1
            ])->id
        ]);


        $this->actingAs($second_user, 'sanctum');
        $this->get(Route('transfer.account_index', $account))->assertStatus(403);


        $this->actingAs($first_user, 'sanctum');
        $this->get(Route('transfer.account_index', $account))->assertStatus(200)->assertJsonStructure([
            []
        ]);
    }

    /**
     * @test
     */
    public function users_can_not_transfer_more_than_account_credit()
    {
        $user = User::factory()->create();
        $credit = rand(2, 20);
        $account = Account::factory()->create(['owner_id' => $user->id, 'credit' => $credit]);

        $transferData = Transfer::factory()->raw(['transaction_id' => null, 'destinationNumber' => 'IR1000000000']);
        $transferData = array_merge($transferData, ['amount' => $credit * 2]);
        $this->actingAs($user, 'sanctum');
        $this->post(Route('transfer.store', $account), $transferData)->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_can_not_transfer_more_than_shaba_maximum_number()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['owner_id' => $user->id, 'credit' => Transfer::PAYA_TRANSFER_MAX_LIMIT + 20]);

        $transferData = Transfer::factory()->raw(['transaction_id' => null, 'destinationNumber' => 'IR1000000000']);
        $transferData = array_merge($transferData, ['amount' => Transfer::PAYA_TRANSFER_MAX_LIMIT + 10]);
        $this->actingAs($user, 'sanctum');
        $this->post(Route('transfer.store', $account), $transferData)->assertStatus(422);
    }


    /**
     * @test
     */
    public function ayande_users_can_not_transfer_at_the_end_of_the_day()
    {
        $limitTime = Carbon::now()->setHour(23)->setMinute(58);
        Carbon::setTestNow($limitTime);

        $user = User::factory()->create();
        $account = Account::factory()->create(['owner_id' => $user->id, 'credit' => 20, 'bank' => Bank::AYANDE]);

        $transferData = Transfer::factory()->raw(['transaction_id' => null]);
        $transferData = array_merge($transferData, ['amount' => 10]);

        $this->actingAs($user, 'sanctum');
        $this->post(Route('transfer.store', $account), $transferData)->assertStatus(500);

    }

    /**
     * @test
     */
    public function users_can_not_have_multi_dash_contbues_in_description()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['owner_id' => $user->id, 'credit' => 20]);

        $transferData = Transfer::factory()->raw(['transaction_id' => null, 'description' => 'test with ---- dashes']);
        $transferData = array_merge($transferData, ['amount' => 10]);

        $this->actingAs($user, 'sanctum');
        $this->post(Route('transfer.store', $account), $transferData)->assertStatus(422);

    }


    /**
     * @test
     */
    public function users_can_transfer()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['owner_id' => $user->id, 'credit' => 20]);

        $transferData = Transfer::factory()->raw(['transaction_id' => null]);
        $transferData = array_merge($transferData, ['amount' => 10]);

        $this->actingAs($user, 'sanctum');
        $this->post(Route('transfer.store', $account), $transferData)->assertStatus(201);
    }

    /**
     * @test
     */
    public function transfer_will_create_a_transaction()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['owner_id' => $user->id, 'credit' => 20]);
        $this->withoutExceptionHandling();

        $transferData = Transfer::factory()->raw(['transaction_id' => null]);
        $transferData = array_merge($transferData, ['amount' => 10]);

        $this->actingAs($user, 'sanctum');
        $this->post(Route('transfer.store', $account), $transferData);

        $this->assertDatabaseCount('transactions', 1);
    }


    /**
     * @test
     */
    public function check_transfer_chnage_the_bank_credit_corrrectlly()
    {
        $user = User::factory()->create();

        $userCredit = rand(31, 100);
        $transferCredit = $userCredit - rand(1, 30);
        $account = Account::factory()->create(['owner_id' => $user->id, 'credit' => $userCredit]);
        $this->withoutExceptionHandling();

        $transferData = Transfer::factory()->raw(['transaction_id' => null]);
        $transferData = array_merge($transferData, ['amount' => $transferCredit]);


        $this->actingAs($user, 'sanctum');
        $this->post(Route('transfer.store', $account), $transferData);

        if (Transaction::first()->first()->status != Transaction::STATUS_FAILED) {
            $this->assertDatabaseHas('accounts', ['credit' => $userCredit - $transferCredit]);
        } else {
            $this->assertDatabaseHas('accounts', ['credit' => $userCredit]);
        }
    }

}
