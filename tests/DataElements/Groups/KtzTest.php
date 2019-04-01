<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\DataElementGroup;
use Abiturma\PhpFints\DataElements\Groups\Kik;
use Abiturma\PhpFints\DataElements\Groups\Ktz;
use Tests\TestCase;

/**
 * Class KtzTest
 * @package Tests\DataElements\Groups
 */
class KtzTest extends TestCase
{

    /** @test */
    public function it_has_sensible_defaults()
    {
        $this->assertEquals('J:iban:bic:0:EUR:280:0', (new Ktz())->toString());
    }

    /** @test */
    public function it_can_be_build_from_a_data_element_group()
    {
        $kik = (new DataElementGroup())->addElement(500)->addElement('myBankCode');
        $preKtz = (new DataElementGroup())
            ->addElement('N')
            ->addElement('myIban')
            ->addElement('myBic')
            ->addElement('myAccountNumber')
            ->addElement('SFR')
            ->addElement($kik);

        $ktz = Ktz::fromDataElementGroup($preKtz);
        $this->assertEquals('N:myIban:myBic:myAccountNumber:SFR:500:myBankCode', $ktz->toString());
    }


    /** @test */
    public function after_built_from_a_data_element_group_the_kik_is_built_correctly()
    {
        $preKtz = (new DataElementGroup())
            ->addElement('N')
            ->addElement('myIban')
            ->addElement('myBic')
            ->addElement('myAccountNumber')
            ->addElement('SFR')
            ->addElement('kik1')
            ->addElement('kik2');

        $ktz = Ktz::fromDataElementGroup($preKtz);
        $this->assertInstanceOf(Kik::class, $ktz->getKik());
    }


    /** @test */
    public function it_returns_the_bank_code_of_its_kik()
    {
        $this->assertEquals('0', (new Ktz())->getBankCode()->toString());
    }
}
