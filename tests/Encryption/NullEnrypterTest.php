<?php

namespace Abiturma\PhpFints\Tests\Encryption;

use Abiturma\PhpFints\Encryption\NullEncrypter;
use Abiturma\PhpFints\Segments\HNHBK;
use Abiturma\PhpFints\Segments\HNVSD;
use Abiturma\PhpFints\Segments\HNVSK;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class NullEnrypterTest
 * @package Tests\Encryption
 */
class NullEnrypterTest extends TestCase
{


    #[Test]
    public function it_returns_head_and_data()
    {
        $result = $this->runEncrypter();
        $this->assertCount(2, $result);
        $this->assertInstanceOf(HNVSK::class, $result[0]);
        $this->assertInstanceOf(HNVSD::class, $result[1]);
    }

    #[Test]
    public function it_builds_the_right_segments_numbers()
    {
        $result = $this->runEncrypter();
        $this->assertEquals(998, $result[0]->getSegmentNumber());
        $this->assertEquals(999, $result[1]->getSegmentNumber());
    }

    #[Test]
    public function the_data_contains_the_unencrypted_data()
    {
        $testString = (new HNHBK())->toString();
        $length = strlen($testString) * 3;
        $encryptedSegment = $this->runEncrypter()[1];
        $this->assertStringEndsWith("@$length@$testString$testString$testString'", $encryptedSegment->toString());
    }

    #[Test]
    public function it_decrypts_an_encrypted_segment()
    {
        $testString = 'thisNeedsToBeDecrypted';
        $this->assertEquals($testString, (new NullEncrypter())->decrypt($testString));
    }


    /**
     * @return array
     */
    protected function runEncrypter()
    {
        $segments = [new HNHBK(), new HNHBK(), new HNHBK()];
        return (new NullEncrypter())->encrypt($segments);
    }

    /**
     * @param $segments
     * @return mixed
     */
    protected function runDecrypter($segments)
    {
        return (new NullEncrypter())->decrypt($segments);
    }
}
