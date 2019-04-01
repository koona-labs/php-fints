<?php

namespace Abiturma\PhpFints\Response\Messages;


use Abiturma\PhpFints\Response\Response;

/**
 * Class AbstractResponseMessage
 * @package Abiturma\PhpFints
 */
abstract class AbstractResponseMessage
{
    /**
     * @var Response 
     */
    protected $response;

    /**
     * AbstractResponseMessage constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->response->$name(...$arguments);
    }
    
}