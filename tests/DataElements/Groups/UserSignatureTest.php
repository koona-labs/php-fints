<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\UserSignature;
use Tests\TestCase;


class UserSignatureTest extends TestCase
{

    
    /** @test */
    public function the_user_signature_has_sensible_defaults()
    {
        $this->assertEquals('pin', (new UserSignature())->toString()); 
    }
    
    /** @test */
    public function it_allows_pin_injection()
    {
        $this->assertEquals('myTestPin', (new UserSignature())->setPin('myTestPin')->toString());
    }

    /** @test */
    public function it_allows_tan_injection()
    {
        $this->assertEquals('pin:myTestTan', (new UserSignature())->setTan('myTestTan')->toString());
    }
    
    

}

