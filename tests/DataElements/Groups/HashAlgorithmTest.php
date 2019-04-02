<?php

namespace Abiturma\PhpFints\Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\HashAlgorithm;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class HashAlgorithmTest
 * @package Tests\DataElements\Groups
 */
class HashAlgorithmTest extends TestCase
{

    
    /** @test */
    public function the_hash_algorithm_has_sensible_defaults()
    {
        $this->assertEquals('1:999:1', (new HashAlgorithm())->toString());
    }
}
