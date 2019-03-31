<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SecurityProfile;
use Tests\TestCase;


class SecurityProfileTest extends TestCase
{

    public function setUp(): void
    {
        parent::setup();
    }
    
    /** @test */
    public function the_security_profile_has_sensible_defaults()
    {
        $this->assertEquals('PIN:1',(new SecurityProfile())->toString()); 
    }
    

}

