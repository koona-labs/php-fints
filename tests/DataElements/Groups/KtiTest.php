<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\Kik;
use Abiturma\PhpFints\DataElements\Groups\Kti;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class KtiTest
 * @package Tests\DataElements\Groups
 */
class KtiTest extends TestCase
{


    #[Test]
    public function it_builds_an_empty_object()
    {
        $this->assertEquals(':::::', (new Kti)->toString());
    }

    #[Test]
    public function all_scalar_values_can_be_injected()
    {
        $kti = (new Kti())->setIban('iban')
            ->setBic('bic')
            ->setAccountNumber('accountNumber')
            ->setSubAccountNumber('subAccountNumber');
        
        $this->assertEquals('iban:bic:accountNumber:subAccountNumber::', $kti->toString());
    }

    #[Test]
    public function a_kik_can_be_injected()
    {
        $kti = (new Kti())->setKik((new Kik())->setBankCode('bankCode'));
        
        $this->assertEquals('::::280:bankCode', $kti->toString());
    }
}
