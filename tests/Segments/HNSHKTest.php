<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\HNSHK;
use Tests\TestCase;


class HNSHKTest extends TestCase
{


    /** @test */
    public function a_signature_head_segment_is_built()
    {
        $segment = new HNSHK();
        $this->assertStringStartsWith('HNSHK:1:4+',$segment->toString()); 
    }
    
    /** @test */
    public function a_username_can_be_injected()
    {
        $segment = (new HNSHK)->setUsername('someRandomUsername'); 
        $this->assertStringContainsString('someRandomUsername',$segment->toString()); 
    }

    /** @test */
    public function a_bank_code_can_be_injected()
    {
        $segment = (new HNSHK)->setBankCode('12345678');
        $this->assertStringContainsString('12345678',$segment->toString());
    }
    
    

}

