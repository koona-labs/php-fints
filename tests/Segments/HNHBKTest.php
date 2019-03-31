<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\AbstractSegment;
use Abiturma\PhpFints\Segments\HNHBK;
use Tests\TestCase;


class HBHBKTest extends TestCase
{

    public function setUp(): void
    {
        parent::setup();
    }
    
    /** @test */
    public function a_message_head_is_built()
    {
        $segment = (new HNHBK)->setMessageLength(89423)->setDialogId(4)->setMessageNumber(3); 
        return $this->assertEquals("HNHBK:1:3+000000089423+300+4+3'",$segment->toString()); 
    }
    

}

