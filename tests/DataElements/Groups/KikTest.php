<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\Kik;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class KikTest
 * @package Tests\DataElements\Groups
 */
class KikTest extends TestCase
{


    #[Test]
    public function the_kik_has_sensible_defaults()
    {
        $this->assertStringStartsWith('280:', (new Kik())->toString());
    }

    #[Test]
    public function it_allows_bank_code_injection()
    {
        $this->assertEquals('280:123456', (new Kik())->setBankCode(123456)->toString());
    }
}
