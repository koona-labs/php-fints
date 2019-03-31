<?php

namespace Tetsts\Response;

use Abiturma\PhpFints\DataElements\Bin;
use Abiturma\PhpFints\Encryption\NullEncrypter;
use Abiturma\PhpFints\Response\Response;
use Abiturma\PhpFints\Response\ResponseFactory;
use Tests\TestCase;


class ResponseFactoryTest extends TestCase
{

    /** @test */
    public function it_returns_a_response()
    {
        $testString = "HKTEST:1:3:5'";
        $this->assertInstanceOf(Response::class, $this->make()->fromString($testString)); 
    }
    
    
    
    /** @test */
    public function it_splits_the_response_into_a_sequence_of_segments()
    {
        $testString = "HKTEST:1:3:5+???@+@13@testtesttest'+123'HKTEST:2:123:5+12'HKTEST:3:2:4+@8@testtest'HKTEST:4:3:1'";
        $this->assertCount(4, $this->make()->fromString($testString)->getSegments());
    }

    /** @test */
    public function it_handles_binaries_correctly()
    {
        $testString = "HKFIRST:1:3:5+???@+@11@firstbinary+123'HKSCND:2:123:5+12'HKTHRD:3:2:4+@13@secondbinary''HKEND:4:3:1'";
        $segments = $this->make()->fromString($testString)->getSegments();
        $this->assertInstanceOf(Bin::class, $segments[0]->getElementAtPosition(3));
        $this->assertEquals("firstbinary",$segments[0]->getElementAtPosition(3)->toRawValue());
    }
    
    
    /** @test */
    public function it_handles_nested_binaries_correctly()
    {
        $testString = "HKTEST:1:3:5+@26@NTEST:12:3:5+@9@innertest''HKEND:2:3:4'";
        $segments = $this->make()->fromString($testString)->getSegments();
        $bin = $segments[0]->getElementAtPosition(2); 
        $this->assertInstanceOf(Bin::class,$bin); 
        $this->assertEquals("NTEST:12:3:5+@9@innertest'",$bin->toRawValue()); 
        
    }
    
    /** @test */
    public function it_handles_multibyte_binaries()
    {
        $testString = "HKTEST:1:3:5+some umlaut mess äöü+@13@moreumlauts:ä'HKEND:2:2:3";
        $segments = $this->make()->fromString($testString)->getSegments();
        $bin = $segments[0]->getElementAtPosition(3);
        $this->assertInstanceOf(Bin::class,$bin);
        $this->assertEquals("moreumlauts:ä",$bin->toRawValue());
    }
    
    
    
    
    /** @test */
    public function it_decrypts_an_encrypted_response()
    {
         
        $encryptedResponse = "HNHBK:1:3:0'"
            ."HNVSK:998:3:0+PIN:1+998+1+1::0+1:20100101:000000+2:2:13:@8@00000000:5:1+280:123456:username:V:0:0+0'"
            ."HNVSD:999:1:0+@40@HKFIRST:2:1:0'HKSECD:3:1:0'HKTHRD:4:1:0''"
            ."HNHBS:5:1:0+1'"; 
        
        $response = $this->make()->fromString($encryptedResponse); 
        $this->assertCount(5,$response->getSegments()); 
        $this->assertEquals('HKFIRST',$response->getSegments()[1]->getType()); 
        $this->assertEquals('HKSECD',$response->getSegments()[2]->getType()); 
        
    }
    

    protected function make()
    {
        return new ResponseFactory(new NullEncrypter()); 
    }

}

