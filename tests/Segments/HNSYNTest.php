<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\HKSYN;
use Tests\TestCase;

/**
 * Class HNSYNTest
 * @package Tests\Segments
 */
class HNSYNTest extends TestCase
{


    /** @test */
    public function a_segment_head_is_built()
    {
        $this->assertStringStartsWith('HKSYN:1:3+', (new HKSYN())->toString());
    }

    /** @test */
    public function it_has_sensible_defaults()
    {
        $this->assertEquals("HKSYN:1:3+0'", (new HKSYN())->toString());
    }
}
