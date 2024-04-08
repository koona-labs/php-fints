<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HKSYN;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HNSYNTest
 * @package Tests\Segments
 */
class HNSYNTest extends TestCase
{


    #[Test]
    public function a_segment_head_is_built()
    {
        $this->assertStringStartsWith('HKSYN:1:3+', (new HKSYN())->toString());
    }

    #[Test]
    public function it_has_sensible_defaults()
    {
        $this->assertEquals("HKSYN:1:3+0'", (new HKSYN())->toString());
    }
}
