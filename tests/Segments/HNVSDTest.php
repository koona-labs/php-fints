<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HNHBK;
use Abiturma\PhpFints\Segments\HNVSD;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class HNVSDTest
 * @package Tests\Segments
 */
class HNVSDTest extends TestCase
{


    /** @test */
    public function an_encrypted_data_segment_is_built()
    {
        $segment = new HNVSD();
        $this->assertStringStartsWith('HNVSD:1:1', $segment->toString());
    }
    
    
    /** @test */
    public function it_holds_encrypted_binary_data()
    {
        $stringToHold = (new HNHBK())->toString();
        $length = strlen($stringToHold);
        $segmentUnderTest = (new HNVSD())->setEncryptedData($stringToHold);

        $this->assertStringEndsWith("@$length@$stringToHold'", $segmentUnderTest->toString());
    }
}
