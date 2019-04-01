<?php

namespace Abiturma\PhpFints\Exceptions;


/**
 * Class MessageHeadMissingException
 * @package Abiturma\PhpFints
 */
class MessageHeadMissingException extends \Exception
{
    public function __construct()
    {
       parent::__construct("Call method newMessage before pushing or prepending segments");      
    }
}