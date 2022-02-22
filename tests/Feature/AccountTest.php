<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use WithFaker, RefreshDatabase;


    /**
     * @test
     */
    public function guess_can_not_see_accounts()
    {

        // $user = User::factory()->create();
        // $this->actingAs($user, 'sanctum');

        $this->get(Route('account.inedx'))->assertStatus(401)->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );

        $account = Account::factory()->create();
        $this->get(Route('account.show', [$account]))->assertStatus(401)->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );


        $this->post(Route('account.store'))->assertStatus(401)->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );

        $this->patch(Route('account.update', [$account]))->assertStatus(401)->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );

        $this->delete(Route('account.destroy', [$account]))->assertStatus(401)->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );

    }

    /**
     * @test
     */
    public function users_can_see_their_own_accounts()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Account::factory(rand(1, 5))->create([
            'owner_id' => $user->id
        ]);

        $this->get(Route('account.inedx'))->assertStatus(200)->assertJsonStructure([
                '*' => [
                    'bank',
                    'credit',
                    'account_number',
                    'shaba_number',
                    'description',
                ]
            ]
        );
    }


    /**
     * @test
     */
    public function user_can_not_see_others_accounts()
    {

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Account::factory(rand(1, 5))->create([]);

        $this->get(Route('account.inedx'))->assertStatus(200)->assertExactJson([]);
    }

    /**
     * @test
     */
    public function users_can_see_their_own_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $account = Account::factory()->create([
            'owner_id' => $user->id
        ]);

        $this->get(Route('account.show', [$account]))
            ->assertStatus(200)
            ->assertJson($account->attributesToArray());
    }


    /**
     * @test
     */
    public function users_can_not_see_others_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $account = Account::factory()->create([]);

        $this->get(Route('account.show', [$account]))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function authenticated_user_can_create_accoutn()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $account = Account::factory()->raw(['owner_id' => $user->id]);

        $this->post(Route('account.store'), $account)->assertStatus(201)->assertJson(['Response' => 'Ok']);
        $this->assertDatabaseHas('accounts', $account);

    }

    /**
     * @test
     */
    public function check_required_validation_for_create_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $required_fields = ['bank', 'account_number', 'shaba_number', 'credit'];

        foreach ($required_fields as $required_field) {
            $account = Account::factory()->raw([$required_field => null]);

            $this->post(Route('account.store', $account))
                ->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors'
                ]);

        }
    }

    /**
     * @test
     */
    public function check_only_owner_of_account_can_update_it()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $account = Account::factory()->create();
        $account->description = "changed";

        $this->patch(Route('account.update', $account))->assertStatus(403);
    }

    /**
     * @test
     */
    public function user_can_only_update_description_of_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $account = Account::factory()->create(['owner_id' => $user->id]);

        $this->patchJson(
            Route('account.update', $account),
            Account::factory()->raw(['description' => $this->faker->sentence])
        )->assertStatus(200);

        $new_account = Account::first();

        $changed_att = array_diff($account->getAttributes(), $new_account->getAttributes());

        $this->assertTrue(count($changed_att) == 1 && $changed_att['description'] != null);

    }

    /**
     * @test
     */
    public function users_can_not_destroy_others_accounts()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $account = Account::factory()->create();

        $this->delete(Route('account.destroy', $account))->assertStatus(403);
    }

    /**
     * @test
     */
    public function users_can_delete_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $account = Account::factory()->create(['owner_id' => $user->id]);

        $this->delete(Route('account.destroy', $account));

        $this->assertTrue(Account::all()->count() == 0);
        $this->assertTrue(Account::onlyTrashed()->count() == 1);
    }
}
