<?php

namespace Abiturma\PhpFints\Tests;

use Abiturma\PhpFints\BaseFints;
use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Fints;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;

/**
 * Class FintsTest
 * @package Tests
 */
class FintsTest extends TestCase
{


    #[Test]
    public function it_builds_a_fully_equipped_hbci_base_class()
    {
        $this->assertInstanceOf(BaseFints::class, Fints::username('testUsername'));
    }

    #[Test]
    public function it_lets_you_inject_a_logger()
    {
        $this->assertInstanceOf(BaseFints::class, Fints::withLogger($this->createMock(LoggerInterface::class)));
    }


    #[Test]
    public function it_lets_you_use_your_own_credentials_store()
    {
        $this->assertInstanceOf(BaseFints::class, Fints::useCredentials($this->createMock(HoldsCredentials::class)));
    }
}
