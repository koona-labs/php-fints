<?php

namespace Abiturma\PhpFints\Adapter;

use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Response\Response;

/**
 * Interface SendsMessages
 * @package Abiturma\PhpFints
 */
interface SendsMessages
{

    /**
     * @param Message $message
     * @return Response
     */
    public function send(Message $message);


    /**
     * @param $host
     * @return $this
     */
    public function to($host);
}
