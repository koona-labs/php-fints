<?php

namespace Tests\DataElements\Groups;

use Abiturma\PhpFints\DataElements\Groups\HashAlgorithm;
use Tests\TestCase;


class HashAlgorithmTest extends TestCase
{

    
    /** @test */
    public function the_hash_algorithm_has_sensible_defaults()
    {
        $this->assertEquals('1:999:1',(new HashAlgorithm())->toString()); 
    }
    

}

