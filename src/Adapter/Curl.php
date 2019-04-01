<?php

namespace Abiturma\PhpFints\Adapter;


use Abiturma\PhpFints\Exceptions\ConnectionFailed;
use Abiturma\PhpFints\Exceptions\HttpException;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Response\Response;
use Abiturma\PhpFints\Response\ResponseFactory;
use Exception;

/**
 * Class Curl
 * @package Abiturma\PhpFints
 */
class Curl implements SendsMessages
{
    /**
     * @var \Curl\Curl 
     */
    protected $curl;

    /**
     * @var boolean
     */
    protected $initialized = false;
    
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;


    /**
     * Curl constructor.
     * @param \Curl\Curl $curl
     * @param ResponseFactory $responseFactory
     */
    public function __construct(\Curl\Curl $curl, ResponseFactory $responseFactory)
    {
        $this->curl = $curl;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Sets the host url
     * 
     * @param $host
     * @return $this
     */
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

    /**
     * @return \Curl\Curl
     */
    public function getCurl()
    {
        return $this->curl;         
    }


    /**
     * @param Message $message
     * @return Response
     * @throws ConnectionFailed
     * @throws HttpException
     */
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