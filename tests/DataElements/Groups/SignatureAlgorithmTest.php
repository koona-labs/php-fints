<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SignatureAlgorithm;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class SecurityAlgorithmTest
 * @package Tests\DataElements\Groups
 */
class SecurityAlgorithmTest extends TestCase
{

    
    /** @test */
    public function the_security_algorithm_has_sensible_defaults()
    {
        $this->assertEquals('6:10:16', (new SignatureAlgorithm())->toString());
    }
}
