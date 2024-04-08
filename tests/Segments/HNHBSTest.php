<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HNHBS;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HBHBSTest
 * @package Tests\Segments
 */
class HNHBSTest extends TestCase
{

    #[Test]
    public function an_end_of_message_segment_is_built()
    {
        $segment = (new HNHBS)->setMessageNumber(3);
        $this->assertEquals("HNHBS:1:1+3'", $segment->toString());
    }
}
