<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HKTAN;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HKIDNTest
 * @package Tests\Segments
 */
class HKTANTest extends TestCase
{

    #[Test]
    public function it_has_sensible_defaults()
    {
        $this->assertEquals("HKTAN:1:6+4+HKIDN'",(new HKTAN())->toString()); 
    }
    
}
