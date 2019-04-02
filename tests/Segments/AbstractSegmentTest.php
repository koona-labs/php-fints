<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\AbstractSegment;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class AbstractSegmentTest
 * @package Tests\Segments
 */
class AbstractSegmentTest extends TestCase
{

    
    /** @test */
    public function a_segment_head_is_built()
    {
        $stub = $this->getMockForAbstractClass(AbstractSegment::class);
        $this->assertEquals("XXXX:1:3'", $stub->toString());
    }
}
