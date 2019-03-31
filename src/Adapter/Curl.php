<?php

namespace Abiturma\PhpFints\Adapter;


use Abiturma\PhpFints\Exceptions\ConnectionFailed;
use Abiturma\PhpFints\Exceptions\HttpException;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Response\ResponseFactory;
use Exception;

class Curl implements SendsMessages
{

    protected $curl; 
    
    protected $initialized = false;
    
    
    protected $responseFactory;

    public function __construct(\Curl\Curl $curl, ResponseFactory $responseFactory)
    {
        $this->curl = $curl;
        $this->responseFactory = $responseFactory;
    }

    public function to($host)
    {
        
        $this->curl->setUserAgent('Laravel-Hbci');
        $this->curl->setUrl($host); 
        $this->curl->setHeaders(["cache-control: no-cache", 'Content-Type: text/plain']);
        $this->curl->setOpt(CURLOPT_SSLVERSION,1); 
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER,true); 
        $this->initialized = true;
        return $this; 
    }

    public function getCurl()
    {
        return $this->curl;         
    }


    public function send(Message $message)
    {
       if(!$this->initialized) {
           throw new Exception('No host set'); 
       }
       $response = $this->curl->post('',$message->toBase64());  
       if(!$response) {
            throw new ConnectionFailed('Cannot connect to host');    
       }
       $statusCode = $this->curl->getHttpStatusCode();  
       if($statusCode < 200 || $statusCode > 299) {
           throw new HttpException('Received status code '. $statusCode); 
       }
       
       return $this->responseFactory->fromBase64($response)->setOriginalOrder($message->getSegmentOrder()); 
       
    }
}