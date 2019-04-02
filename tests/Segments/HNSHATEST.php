<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HNSHA;
use Abiturma\PhpFints\Segments\HNSHK;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class HNSHATEST
 * @package Tests\Segments
 */
class HNSHATEST extends TestCase
{


    /** @test */
    public function an_end_of_signature_segment_is_built()
    {
        $this->assertStringStartsWith('HNSHA:1:2+', (new HNSHA)->toString());
    }
    
    /** @test */
    public function it_handles_a_pin()
    {
        $pin = 'mySecretPin';
        $this->assertEquals("HNSHA:1:2+++$pin'", (new HNSHA())->setPin($pin)->toString());
    }
    
    /** @test */
    public function it_handles_pin_tan()
    {
        $pin = 'mySecretPin';
        $tan = 'mySecretTan';
        $this->assertEquals("HNSHA:1:2+++$pin:$tan'", (new HNSHA())->setPin($pin)->setTan($tan)->toString());
    }
    
    /** @test */
    public function it_sets_the_security_control_reference_accordingly()
    {
        $head = new HNSHK();
        $end = new HNSHA();
        $reference = $head->getSecurityControlReference();
        $end->setSecurityControlReference($head);
        $this->assertEquals("HNSHA:1:2+$reference++pin'", $end->toString());
    }
}
