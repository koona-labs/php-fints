<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SecurityDateTime;
use DateTime;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class SecurityDateTimeTest
 * @package Tests\DataElements\Groups
 */
class SecurityDateTimeTest extends TestCase
{

    
    /** @test */
    public function the_security_date_has_sensible_defaults()
    {
        $now = new DateTime();
        $expected = '1:'. $now->format('Ymd') .':'. $now->format('His');
        $this->assertEquals($expected, (new SecurityDateTime())->toString());
    }
}
