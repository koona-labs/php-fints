<?php

namespace Abiturma\PhpFints\Tests\Models;

use Abiturma\PhpFints\Models\Transaction;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class TransactionTest
 * @package Tests\Models
 */
class TransactionTest extends TestCase
{

    #[Test]
    public function it_casts_the_amount_in_cents_to_a_two_digit_float()
    {
        $transactions = new Transaction(['base_amount' => -1203456]);
        $this->assertEquals(-12034.56, $transactions->amount);
    }


    #[Test]
    public function it_parses_to_an_array_of_attributes()
    {
        $transactions = new Transaction(['base_amount' => -12034]);
        $this->assertEquals(['base_amount' => -12034, 'amount' => -120.34], $transactions->toArray());
    }
}
