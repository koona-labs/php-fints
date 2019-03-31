<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\HKEND;
use Tests\TestCase;


class HKENDTest extends TestCase
{


    
    /** @test */
    public function it_builds_an_end_of_dialog_segment()
    {
        $this->assertEquals("HKEND:1:1+myTestId'", (new HKEND())->setDialogId('myTestId')->toString()); 
    }
    
}

