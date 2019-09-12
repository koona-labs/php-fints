<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HKVVB;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class HKVVBTest
 * @package Tests\Segments
 */
class HKVVBTest extends TestCase
{

    /** @test */
    public function a_segment_head_is_built()
    {
        $this->assertStringStartsWith('HKVVB:1:3+', (new HKVVB())->toString());
    }

    /** @test */
    public function it_has_sensible_defaults()
    {
        $this->assertStringStartsWith("HKVVB:1:3+0+0+0+580", (new HKVVB())->toString());
    }
}
