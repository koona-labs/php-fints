<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SecurityIdentificationDetails;
use Tests\TestCase;


class SecurityIdentificationDetailsTest extends TestCase
{

    
    /** @test */
    public function the_security_date_has_sensible_defaults()
    {
        $this->assertEquals('1::0', (new SecurityIdentificationDetails())->toString());     
    }
    

}

