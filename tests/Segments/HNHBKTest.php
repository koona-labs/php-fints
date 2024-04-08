<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HNHBK;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HBHBKTest
 * @package Tests\Segments
 */
class HNHBKTest extends TestCase
{

    #[Test]
    public function a_message_head_is_built()
    {
        $segment = (new HNHBK)->setMessageLength(89423)->setDialogId(4)->setMessageNumber(3);
        $this->assertEquals("HNHBK:1:3+000000089423+300+4+3'", $segment->toString());
    }
}
