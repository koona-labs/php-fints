<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SignatureAlgorithm;
use Tests\TestCase;


class SecurityAlgorithmTest extends TestCase
{

    public function setUp(): void
    {
        parent::setup();
    }
    
    /** @test */
    public function the_security_algorithm_has_sensible_defaults()
    {
        $this->assertEquals('6:10:16',(new SignatureAlgorithm())->toString()); 
    }
    

}

