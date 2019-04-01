<?php

namespace Tests\Credentials;

use Abiturma\PhpFints\Credentials\CredentialsContainer;
use Tests\TestCase;


class CredentialsContainerTest extends TestCase
{

    
    protected $credentials; 
    
    
    public function setUp(): void
    {

        parent::setup();
        $this->credentials = new CredentialsContainer(); 
    }
    
    
    
    /** @test */
    public function it_stores_all_credentials_correctly()
    {
        $this->setValue('host','TestHost')->assertValue('host','TestHost'); 
        $this->setValue('port','TestPort')->assertValue('port','TestPort');
        $this->setValue('bankCode','TestBankCode')->assertValue('bankCode','TestBankCode');
        $this->setValue('username','TestUsername')->assertValue('username','TestUsername');
        $this->setValue('pin','TestPin')->assertValue('pin','TestPin');
        
    }

    
    
    


    protected function setValue($setter,$value)
    {
        $setter = 'set'. ucfirst($setter); 
        $this->credentials->$setter($value); 
        return $this; 
    }

    protected function assertValue($method,$value)
    {
        $this->assertEquals($this->credentials->$method(),$value); 
    }


    
    

}

