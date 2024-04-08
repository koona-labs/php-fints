<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Segments\HKIDN;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HKIDNTest
 * @package Tests\Segments
 */
class HKIDNTest extends TestCase
{

    #[Test]
    public function a_segment_head_is_built()
    {
        $this->assertStringStartsWith('HKIDN:1:2+', (new HKIDN())->toString());
    }

    #[Test]
    public function it_has_sensible_defaults()
    {
        $this->assertEquals("HKIDN:1:2+280:0+username+0+1'", (new HKIDN())->toString());
    }

    #[Test]
    public function it_injects_a_username()
    {
        $username = 'myTestUsername';
        $this->assertStringContainsString($username, (new HKIDN())->setUsername($username)->toString());
    }

    #[Test]
    public function it_injects_a_bankcode()
    {
        $bankCode = 123456789;
        $this->assertStringContainsString($bankCode, (new HKIDN())->setBankCode($bankCode)->toString());
    }

    #[Test]
    public function it_injects_a_system_id()
    {
        $systemId = 123321;
        $this->assertStringContainsString($systemId, (new HKIDN())->setSystemId($systemId)->toString());
    }


    #[Test]
    public function it_can_be_build_from_credentials()
    {
        $username = 'myTestUsername';
        $bankCode = 123321;
        $credentials = $this->createMock(HoldsCredentials::class);
        $credentials->method('username')->willReturn($username);
        $credentials->method('bankCode')->willReturn($bankCode);
        
        $hkidn = (new HKIDN())->fromCredentials($credentials);
        
        $this->assertStringContainsString("280:$bankCode+$username", $hkidn->toString());
    }
}
