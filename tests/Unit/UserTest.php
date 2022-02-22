<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function user_has_accounts()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(Collection::class, $user->accounts);
    }

    /** @test */
    public function user_has_transactions()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(Collection::class, $user->transactions);
    }

    /** @test */
    public function user_has_transfers()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(Collection::class, $user->transfers);
    }


}
