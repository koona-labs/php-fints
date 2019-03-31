<?php

namespace Tests\Response;

use Abiturma\PhpFints\Response\Feedback;
use Abiturma\PhpFints\Response\Response;
use Abiturma\PhpFints\Response\ResponseSegment;
use Tests\TestCase;


class ResponseTest extends TestCase
{

    /** @test */
    public function it_builds_a_response_from_segments()
    {
        $this->assertInstanceOf(Response::class, $this->fromSegments(['HKTEST:1:2:3'])); 
    }

    /** @test */
    public function it_returns_segments_of_a_given_type()
    {
        
        $response = $this->fromSegments(['HKSTART:1:1:1','HKTEST:2:12:3','HKEND:3:1:1']); 
        $this->assertInstanceOf(ResponseSegment::class,$response->getFirstOfType('hktest')); 
        $this->assertEquals(12,$response->getFirstOfType('hktest')->getVersion()); 
        
    }

    /** @test */
    public function if_the_type_doesnt_exists_it_returns_null_instead()
    {
        $response = $this->fromSegments(['HKSTART:1:1:1']); 
        $this->assertNull($response->getFirstOfType('hktest')); 
    }
    
    /** @test */
    public function it_returns_general_feedback()
    {
        $response = $this->fromSegments('HIRMG:1:2:3'); 
        $this->assertInstanceOf(Feedback::class,$response->getGeneralFeedback()); 
        
    }
    
    /** @test */
    public function it_returns_segmental_feedback()
    {
        $response = $this->fromSegments(['HIRMS:1:2:3','HIRMS:1:2:3','MISC:1:2:3']); 
        $feedback = $response->getSegmentalFeedback(); 
        $this->assertCount(2,$feedback);
        $this->assertInstanceOf(Feedback::class,$feedback[0]); 
    }
    
    /** @test */
    public function it_returns_a_full_error_message()
    {
        $response = $this->fromSegments([
            'HIRMG:1:2:3+1234::Test1',
            'HIRMS:1:2:3+1234::Test2',
            'HIRMS:1:2:3+1234::Test3',
            'MIS:1:2:3',
        ]);
        
        $this->assertEquals('Test1|||Test2||Test3',$response->getFullErrorMessage()); 
    }
    
    /** @test */
    public function it_checks_if_it_is_ok()
    {
        $response = $this->fromSegments('HIRMG:1:2:3+9000::Test1'); 
        $this->assertFalse($response->isOk());
        $response = $this->fromSegments('HIRMG:1:2:3+0200::Test1');
        $this->assertTrue($response->isOk()); 
        
    }

    /** @test */
    public function it_checks_if_it_has_warnings()
    {
        $response = $this->fromSegments('HIRMG:1:2:3+0100::Test1');
        $this->assertFalse($response->hasWarnings());
        $response = $this->fromSegments('HIRMG:1:2:3+3200::Test1');
        $this->assertTrue($response->hasWarnings());
    }
    
    /** @test */
    public function it_returns_feedback_by_the_name_of_the_original_segment()
    {
        $originalOrder = [ 1 => 'HFIRST', 2 => 'HSECOND', 3 => 'HTHIRD']; 
        $response = $this->fromSegments('HIRMS:1:2:2+1234::TestMessage')
            ->setOriginalOrder($originalOrder); 
        
        $this->assertNull($response->getFeedbackBySegmentName('HFIRST')); 
        $feedback = $response->getFeedbackBySegmentName('HSECOND'); 
        $this->assertInstanceOf(Feedback::class,$feedback);
        $this->assertEquals('1234',$feedback->getCode()); 
    }
    
    /** @test */
    public function it_checks_if_it_is_paginated()
    {
        $originalOrder = [ 1 => 'HPAGE',2 => 'HNOPAGE'];
        $response = $this->fromSegments([
            'HIRMS:1:2:1+3040::TestMessage',
            'HIRMS:1:2:2+3041::TestMessage',
        ])
            ->setOriginalOrder($originalOrder);
        $this->assertTrue($response->isPaginated('HPAGE')); 
        $this->assertFalse($response->isPaginated('HNOPAGE')); 
    }
    
    /** @test */
    public function it_returns_a_pagination_token()
    {
        $originalOrder = [ 1 => 'HPAGE',2 => 'HNOPAGE'];
        $response = $this->fromSegments([
            'HIRMS:1:2:1+3040::TestMessage:testToken',
            'HIRMS:1:2:2+3041::TestMessage:noToken',
        ])
            ->setOriginalOrder($originalOrder);
        $this->assertEquals('testToken',$response->getPaginationToken('HPAGE'));
        $this->assertNull($response->getPaginationToken('HNOPAGE'));
    }
    
    
    



    protected function fromSegments($segments = [])
    {
        if(!is_array($segments)) {
            $segments = [$segments]; 
        }
        
        $parsedSegments = array_map(function($segment) {
            return ResponseSegment::parseFromString($segment); 
        },$segments); 
        return Response::fromSegments($parsedSegments); 
    }
    
    
    
    
}

