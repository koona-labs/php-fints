<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\HKVVB;
use Tests\TestCase;


class HKVVBTest extends TestCase
{

    /** @test */
    public function a_segment_head_is_built()
    {
        $this->assertStringStartsWith('HKVVB:1:3+',(new HKVVB())->toString());
    }

    /** @test */
    public function it_has_sensible_defaults()
    {
        $this->assertEquals("HKVVB:1:3+0+0+0+laravel-hbci+0.8'",(new HKVVB())->toString());
    }

}

