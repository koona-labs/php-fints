<?php

namespace Tests\Segments;

use Abiturma\PhpFints\Segments\HKSPA;
use Tests\TestCase;


class HKSPATest extends TestCase
{

    
    /** @test */
    public function a_sepa_accounts_segment_is_built()
    {
        $this->assertEquals("HKSPA:1:1'", (new HKSPA())->toString()); 
    }
    
    

}

