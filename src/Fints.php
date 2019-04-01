<?php

namespace Abiturma\PhpFints;

use Abiturma\PhpFints\Adapter\Curl;
use Abiturma\PhpFints\Credentials\CredentialsContainer;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Encryption\NullEncrypter;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Message\MessageBuilder;
use Abiturma\PhpFints\Misc\NullLogger;
use Abiturma\PhpFints\Response\ResponseFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Fints
 * @package Abiturma\PhpFints
 */
class Fints
{

    /**
     * @param LoggerInterface|null $logger
     * @return BaseFints
     * @throws \ErrorException
     */
    protected static function build(LoggerInterface $logger = null)
    {
        if (!$logger) {
            $logger = new NullLogger();
        }
        
        $encrypter = new NullEncrypter();
        
        $responseFactory = new ResponseFactory($encrypter);
        $adapter = new Curl(new \Curl\Curl(), $responseFactory);
        
        $message = new Message($encrypter);
        $messageBuilder = new MessageBuilder($message);
        
        $dialogParameters = new DialogParameters();
        
        $dialog = new Dialog($adapter, $messageBuilder, $dialogParameters, $logger);
        
        $credentials = new CredentialsContainer();
        return new BaseFints($credentials, $dialog);
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \ErrorException
     */
    public static function __callStatic($name, $arguments)
    {
        return self::build()->$name(...$arguments);
    }
}
