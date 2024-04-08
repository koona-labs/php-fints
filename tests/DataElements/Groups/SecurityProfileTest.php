<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SecurityProfile;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class SecurityProfileTest
 * @package Tests\DataElements\Groups
 */
class SecurityProfileTest extends TestCase
{


    #[Test]
    public function the_security_profile_has_sensible_defaults()
    {
        $this->assertEquals('PIN:1', (new SecurityProfile())->toString());
    }
}
