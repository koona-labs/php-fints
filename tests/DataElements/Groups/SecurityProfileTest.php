<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SecurityProfile;
use Tests\TestCase;

/**
 * Class SecurityProfileTest
 * @package Tests\DataElements\Groups
 */
class SecurityProfileTest extends TestCase
{

    
    /** @test */
    public function the_security_profile_has_sensible_defaults()
    {
        $this->assertEquals('PIN:1', (new SecurityProfile())->toString());
    }
}
