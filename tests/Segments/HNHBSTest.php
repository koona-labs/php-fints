<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\HNHBS;
use Tests\TestCase;

/**
 * Class HBHBSTest
 * @package Tests\Segments
 */
class HBHBSTest extends TestCase
{

    /** @test */
    public function an_end_of_message_segment_is_built()
    {
        $segment = (new HNHBS)->setMessageNumber(3);
        $this->assertEquals("HNHBS:1:1+3'", $segment->toString());
    }
}
