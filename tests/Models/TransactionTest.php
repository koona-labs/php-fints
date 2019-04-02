<?php

namespace Abiturma\PhpFints\Tests\Models;

use Abiturma\PhpFints\Models\Transaction;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class TransactionTest
 * @package Tests\Models
 */
class TransactionTest extends TestCase
{

    /** @test */
    public function it_casts_the_amount_in_cents_to_a_two_digit_float()
    {
        $transactions = new Transaction(['base_amount' => -12034]);
        $this->assertEquals(-120.34, $transactions->amount);
    }
    
    /** @test */
    public function it_parses_to_an_array_of_attributes()
    {
        $transactions = new Transaction(['base_amount' => -12034]);
        $this->assertEquals(['base_amount' => -12034, 'amount' => -120.34], $transactions->toArray());
    }
}
