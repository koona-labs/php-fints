<?php

namespace Abiturma\PhpFints\Exceptions;


class MessageHeadMissingException extends \Exception
{
    public function __construct()
    {
       parent::__construct("Call method newMessage before pushing or prepending segments");      
    }
}