<?php

namespace Abiturma\PhpFints\Adapter;


use Abiturma\PhpFints\Message\Message;

interface SendsMessages
{

    /**
     * @param Message $message
     * @return Response
     */
    public function send(Message $message);


    /**
     * @param $host
     * @return SendsMessages
     */
    public function to($host); 
    
}