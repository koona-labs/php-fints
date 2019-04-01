<?php

namespace Tests\Models;

use Abiturma\PhpFints\DataElements\Groups\Kti;
use Abiturma\PhpFints\DataElements\Groups\Ktv;
use Abiturma\PhpFints\DataElements\Groups\Ktz;
use Abiturma\PhpFints\Models\Account;
use Tests\TestCase;


/**
 * Class AccountTest
 * @package Tests\Models
 */
class AccountTest extends TestCase
{

    protected $testAttributes; 
    
    public function setUp(): void
    {

        parent::setup();
        
        $this->testAttributes = [
            'bank_code' => 'testBankCode',
            'account_number' => 'testAccountNumber',
            'bic' => 'testBic',
            'iban' => 'testIban'
        ]; 
    }
    
    /** @test */
    public function it_can_be_built_from_a_ktz()
    {
        $ktz = (new Ktz())
            ->setBankCode('testBankCode')
            ->setAccountNumber('testAccountNumber')
            ->setBic('testBic')
            ->setIban('testIban'); 
        
        $account = Account::fromKtz($ktz); 
        
        $this->assertEquals($this->testAttributes, $account->toArray()); 
        
    }
    
    /** @test */
    public function it_transforms_itself_to_a_ktv()
    {
        $account = new Account($this->testAttributes); 
        $this->assertInstanceOf(Ktv::class,$account->toKtv()); 
        $this->assertEquals('testAccountNumber:EUR:280:testBankCode',$account->toKtv()->toString()); 
        
    }
    
    /** @test */
    public function it_transforms_itself_to_kti()
    {
        $account = new Account($this->testAttributes);
        $this->assertInstanceOf(Kti::class,$account->toKti());
        $this->assertEquals('testIban:testBic:testAccountNumber::280:testBankCode',$account->toKti()->toString());
    }
    
    
    

}

