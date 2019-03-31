<?php

namespace Tests\Response\Messages;

use Abiturma\PhpFints\Response\Messages\SyncResponse;
use Abiturma\PhpFints\Response\Response;
use Tests\TestCase;


class SyncResponseTest extends TestCase
{

    protected $response;

    public function setUp(): void
    {
        parent::setup();
        $this->response = $this->createMock(Response::class);
    }


    /** @test */
    public function it_returns_bpd_and_upd()
    {
        $this->response->expects($this->exactly(2))
            ->method('getFirstOfType')
            ->withConsecutive(['HIBPA'],['HIUPA'])
            ->will($this->onConsecutiveCalls('testBpd','testUpd'));

        $sync = $this->make(); 
        $this->assertEquals('testBpd',$sync->getBpd()); 
        $this->assertEquals('testUpd',$sync->getUpd()); 
    }


    protected function make()
    {
        return new SyncResponse($this->response);
    }


}

