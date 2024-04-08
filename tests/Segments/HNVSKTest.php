<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HNSHK;
use Abiturma\PhpFints\Segments\HNVSK;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HNVSKTest
 * @package Tests\Segments
 */
class HNVSKTest extends TestCase
{

    #[Test]
    public function a_encryption_head_segment_is_built()
    {
        $segment = new HNVSK();
        $this->assertStringStartsWith('HNVSK:1:3+', $segment->toString());
    }

    #[Test]
    public function it_has_sensible_defaults()
    {
        $segment = new HNVSK();
        $this->assertStringStartsWith('HNVSK:1:3+PIN:1+998+1', $segment->toString());
        $this->assertStringEndsWith("2:2:13:@8@00000000:5:1+280:0:Username:S:0:0+0'", $segment->toString());
    }


    #[Test]
    public function it_can_be_created_with_the_signature_heads_data()
    {
        $username = 'testUsername';
        $bankCode = 11223344;
        $signatureHead = (new HNSHK())->setUsername($username)->setBankCode($bankCode);
        $segment = (new HNVSK())->fromSignatureHead($signatureHead);
        $this->assertStringContainsString($username, $segment->toString());
        $this->assertStringContainsString($bankCode, $segment->toString());
    }
}
