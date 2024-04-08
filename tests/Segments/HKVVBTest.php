<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HKVVB;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HKVVBTest
 * @package Tests\Segments
 */
class HKVVBTest extends TestCase
{

    #[Test]
    public function a_segment_head_is_built()
    {
        $this->assertStringStartsWith('HKVVB:1:3+', (new HKVVB())->toString());
    }

    #[Test]
    public function it_has_sensible_defaults()
    {
        $this->assertStringStartsWith("HKVVB:1:3+0+0+0+580", (new HKVVB())->toString());
    }
}
