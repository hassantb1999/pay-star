<?php

namespace Tests\Unit;

use App\Modules\Banking\Models\Bank;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    /** @test */
    public function tehre_is_at_least_one_bank_available()
    {
        $this->assertNotEmpty(Bank::getBanks());
    }
}
