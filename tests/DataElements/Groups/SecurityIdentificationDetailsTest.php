<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SecurityIdentificationDetails;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class SecurityIdentificationDetailsTest
 * @package Tests\DataElements\Groups
 */
class SecurityIdentificationDetailsTest extends TestCase
{


    #[Test]
    public function the_security_date_has_sensible_defaults()
    {
        $this->assertEquals('1::0', (new SecurityIdentificationDetails())->toString());
    }
}
