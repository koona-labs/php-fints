<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\AbstractSegment;
use Tests\TestCase;


class AbstractSegmentTest extends TestCase
{

    
    /** @test */
    public function a_segment_head_is_built()
    {
        $stub = $this->getMockForAbstractClass(AbstractSegment::class);
        $this->assertEquals("XXXX:1:3'",$stub->toString());
    }
    
    

}

