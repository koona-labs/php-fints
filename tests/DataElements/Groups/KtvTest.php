<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\Ktv;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class KtvTest
 * @package Tests\DataElements\Groups
 */
class KtvTest extends TestCase
{

    #[Test]
    public function it_has_sensible_defaults()
    {
        $this->assertEquals('0:EUR:280:0', (new Ktv)->toString());
    }

    #[Test]
    public function a_bank_code_can_be_injected()
    {
        $this->assertEquals('0:EUR:280:12345678', (new Ktv)->setBankCode(12345678)->toString());
    }

    #[Test]
    public function an_account_number_can_be_injected()
    {
        $this->assertEquals('12345678:EUR:280:0', (new Ktv)->setAccountNumber(12345678)->toString());
    }
}
