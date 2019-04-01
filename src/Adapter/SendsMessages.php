<?php

namespace Abiturma\PhpFints\Adapter;


use Abiturma\PhpFints\Message\Message;

interface SendsMessages
{

    public function send(Message $message);


    public function to($host); 
    
}