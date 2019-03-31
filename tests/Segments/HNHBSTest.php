<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\HNHBS;
use Tests\TestCase;


class HBHBSTest extends TestCase
{

    public function setUp(): void
    {
        parent::setup();
    }
    
    /** @test */
    public function an_end_of_message_segment_is_built()
    {
        $segment = (new HNHBS)->setMessageNumber(3); 
        $this->assertEquals("HNHBS:1:1+3'",$segment->toString()); 
    }
    

}

