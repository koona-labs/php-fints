<?php

namespace Tests\Adapter;


use Abiturma\PhpFints\Adapter\Curl;
use Abiturma\PhpFints\Exceptions\ConnectionFailed;
use Abiturma\PhpFints\Exceptions\HttpException;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Response\ResponseFactory;
use Exception;
use Tests\TestCase;

class CurlTest extends TestCase 
{
    
    protected $curl; 
    
    protected $message; 
    
    protected $responseFactory; 
    
    public function setUp() :void 
    {
        
        parent::setUp(); 
            
        $this->message = $this->createMock(Message::class); 
        $this->curl = $this->createMock(\Curl\Curl::class); 
        $this->responseFactory = $this->createMock(ResponseFactory::class); 
        
    }
        
        
    /** @test */
    public function it_instantiates_the_right_class()
    {
        $this->assertInstanceOf(Curl::class, $this->make()); 
    }
    
    /** @test */
    public function it_throws_an_exception_if_no_host_is_set()
    {
        $this->expectException(Exception::class); 
        $this->make()->send($this->message); 
    }
    
    /** @test */
    public function it_accepts_a_url()
    {
        $host = 'https://test.test'; 
        $this->curl->expects($this->once())->method('setUrl')->with($this->equalTo($host)); 
        $this->make()->to($host); 
    }
    
    /** @test */
    public function it_throws_a_connection_if_its_connected_to_a_nonexistent_host()
    {
        $host = 'https://does-not-exists.com';
        $this->curl->method('post')->willReturn(null); 
        $this->expectException(ConnectionFailed::class); 
        $this->make()->to($host)->send($this->message); 
    }
    
    /** @test */
    public function it_returns_an_exception_if_the_response_status_code_is_not_2xx()
    {
        $host = 'https://my-host.com';
        $this->curl->method('post')->willReturn('check');
        $this->curl->method('getHttpStatusCode')->willReturn(300); 
        $this->expectException(HttpException::class);
        $this->make()->to($host)->send($this->message);
    }
    



    public function make()
    {   
        return new Curl($this->curl,$this->responseFactory); 
    }
}