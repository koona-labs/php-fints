<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HKTAN;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class HKIDNTest
 * @package Tests\Segments
 */
class HKTANTest extends TestCase
{

    /** @test */
    public function it_has_sensible_defaults()
    {
        $this->assertEquals("HKTAN:1:6+4+HKIDN'",(new HKTAN())->toString()); 
    }
    
}
