<?php

namespace Abiturma\PhpFints\Response\Messages;


use Abiturma\PhpFints\Response\Response;

abstract class AbstractResponseMessage
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function __call($name, $arguments)
    {
        return $this->response->$name(...$arguments);
    }
    
}