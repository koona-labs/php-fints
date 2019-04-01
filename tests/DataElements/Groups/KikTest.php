<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\Kik;
use Tests\TestCase;


class KikTest extends TestCase
{

    
    /** @test */
    public function the_kik_has_sensible_defaults()
    {
        $this->assertStringStartsWith('280:',(new Kik())->toString()); 
    }
    
    /** @test */
    public function it_allows_bank_code_injection()
    {
        $this->assertEquals('280:123456', (new Kik())->setBankCode(123456)->toString()); 
    }
    
    

}

