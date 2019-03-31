<?php

namespace Tests\Dialog;

use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Encryption\NullEncrypter;
use Abiturma\PhpFints\Response\Response;
use Tests\TestCase;


class DialogParametersTest extends TestCase
{

    /** @test */
    public function if_no_values_are_set_it_returns_default_values()
    {
        $parameters = new DialogParameters();
        $this->assertEquals(1,$parameters->messageNumber); 
    }
    
    /** @test */
    public function it_stores_values()
    {
        $parameters = new DialogParameters(); 
        $parameters->setMessageNumber(12); 
        $this->assertEquals(12,$parameters->messageNumber); 
    }
    
    /** @test */
    public function the_stored_values_can_be_reset()
    {
        $parameters = new DialogParameters();
        $parameters->setMessageNumber(12)->reset();
        $this->assertEquals(1,$parameters->messageNumber); 
    }
    
    /** @test */
    public function it_can_be_build_from_a_response()
    {
        $response = $this->createMock(Response::class); 
        $response->method('toMergableParameters')->willReturn(['systemId' => 12, 'messageNumber' => 20, 'shouldNotBeStored' => 'test']); 
        $parameters = (new DialogParameters())->mergeResponse($response); 
        $this->assertEquals(12, $parameters->systemId); 
        $this->assertEquals(20, $parameters->messageNumber);
        $this->assertNull($parameters->shouldNotBeStored); 
        
    }
    
    /** @test */
    public function the_values_from_a_response_can_be_trimmed()
    {
        $response = $this->createMock(Response::class);
        $response->method('toMergableParameters')->willReturn(['systemId' => 12, 'messageNumber' => 20, 'shouldNotBeStored' => 'test']);
        $parameters = (new DialogParameters())->mergeResponse($response,['systemId']);
        $this->assertEquals(0, $parameters->systemId);
        $this->assertEquals(20, $parameters->messageNumber);
        $this->assertNull($parameters->shouldNotBeStored);
    }
    
    /** @test */
    public function only_specific_values_from_a_response_can_be_merged()
    {
        $response = $this->createMock(Response::class);
        $response->method('toMergableParameters')->willReturn(['systemId' => 12, 'messageNumber' => 20, 'shouldNotBeStored' => 'test']);
        $parameters = (new DialogParameters())->mergeResponseOnlyWith($response,['systemId']);
        $this->assertEquals(12, $parameters->systemId);
        $this->assertEquals(1, $parameters->messageNumber);
        $this->assertNull($parameters->shouldNotBeStored);
    }
    
    
    
    
    
    

}

