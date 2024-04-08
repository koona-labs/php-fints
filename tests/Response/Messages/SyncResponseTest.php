<?php

namespace Abiturma\PhpFints\Tests\Response\Messages;

use Abiturma\PhpFints\Response\Messages\SyncResponse;
use Abiturma\PhpFints\Response\Response;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class SyncResponseTest
 * @package Tests\Response\Messages
 */
class SyncResponseTest extends TestCase
{
    protected $response;

    public function setUp(): void
    {
        parent::setup();
        $this->response = $this->createMock(Response::class);
    }


    #[Test]
    public function it_returns_bpd_and_upd()
    {
        $this->response->expects($this->exactly(2))
            ->method('getFirstOfType')
            ->willReturnMap([
                ['HIBPA', 'testBpd'],
                ['HIUPA', 'testUpd']
            ]);

        $sync = $this->make();
        $this->assertEquals('testBpd', $sync->getBpd());
        $this->assertEquals('testUpd', $sync->getUpd());
    }


    /**
     * @return SyncResponse
     */
    protected function make()
    {
        return new SyncResponse($this->response);
    }
}
