<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\SignatureAlgorithm;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class SecurityAlgorithmTest
 * @package Tests\DataElements\Groups
 */
class SignatureAlgorithmTest extends TestCase
{


    #[Test]
    public function the_security_algorithm_has_sensible_defaults()
    {
        $this->assertEquals('6:10:16', (new SignatureAlgorithm())->toString());
    }
}
