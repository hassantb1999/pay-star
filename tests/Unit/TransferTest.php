<?php

namespace Tests\Unit;

use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Transaction;
use App\Modules\Banking\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_transaction()
    {

        $transfer = Transfer::factory()->create();

        $this->assertInstanceOf(Transaction::class, $transfer->transaction);
    }

    /**
     * @test
     */
    public function check_create_transactoin_with_creating_transfer()
    {

        $account = Account::factory()->create(['credit' => 20]);
        $transferReq = Transfer::factory()->raw(['transaction_id' => null, 'amount' => 10]);
        $transfer = resolve(Transfer::class);

        $transfer->createTransfer($account, $transferReq);
        $this->assertDatabaseHas('transactions', $transfer->transaction->getAttributes());

        return true;

    }

    /** @test */
    public function check_track_id_will_generate_uniq_by_creating_50_in_a_second()
    {
        Transfer::factory(50)->create();
        $disCount = Transfer::select('track_id')->distinct()->count();
        $this->assertTrue($disCount == 50);
    }

    /** @test */
    public function check_the_cycle_end_point_time_for_a_transfer()
    {
        $transfer = Transfer::factory()->create();

        $tc = $transfer->getPayaCycleEndPoint();

        $tcC = Carbon::create($tc);

        $this->withoutExceptionHandling();
        $this->assertEquals(45, $tcC->minute);
        $this->assertTrue($tcC->hour == 3 || $tcC->hour == 10 || $tcC->hour == 13);
        $this->assertTrue($tcC->timestamp < time() + 77460); // 21h 31min = 77460
    }


}
