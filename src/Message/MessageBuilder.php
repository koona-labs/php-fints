<?php

namespace Abiturma\PhpFints\Message;

use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Exceptions\DialogMissingException;
use Abiturma\PhpFints\Models\Account;
use Abiturma\PhpFints\Segments\HKCAZ;
use Abiturma\PhpFints\Segments\HKEND;
use Abiturma\PhpFints\Segments\HKIDN;
use Abiturma\PhpFints\Segments\HKKAZ;
use Abiturma\PhpFints\Segments\HKSPA;
use Abiturma\PhpFints\Segments\HKSYN;
use Abiturma\PhpFints\Segments\HKVVB;

/**
 * Class MessageBuilder
 * @package Abiturma\PhpFints
 */
class MessageBuilder
{

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var null|DialogParameters
     */
    protected $dialogParameters = null;

    /**
     * @var null|HoldsCredentials
     */
    protected $credentials = null;

    /**
     * MessageBuilder constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @param Dialog $dialog
     * @return $this
     */
    public function fromDialog(Dialog $dialog)
    {
        $this->dialogParameters = $dialog->getDialogParameters();
        $this->credentials = $dialog->getCredentials();
        return $this;
    }


    /**
     * @return Message
     * @throws DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    public function sync()
    {
        return $this->newMessage()
            ->push((new HKIDN())->fromCredentials($this->credentials))
            ->push(new HKVVB)
            ->push(new HKSYN)
            ->addSignature()
            ->mergeDialogParameters($this->dialogParameters)
            ->encrypt()
            ->prepare();
    }


    /**
     * @return Message
     * @throws DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    public function init()
    {
        return $this->newMessage()
            ->push((new HKIDN())->fromCredentials($this->credentials))
            ->push(new HKVVB)
            ->addSignature()
            ->mergeDialogParameters($this->dialogParameters)
            ->encrypt()
            ->prepare();
    }

    /**
     * @return Message
     * @throws DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    public function getAccounts()
    {
        return $this->newMessage(new HKSPA)
            ->addSignature()
            ->mergeDialogParameters($this->dialogParameters)
            ->encrypt()
            ->prepare();
    }

    /**
     * @param Account $account
     * @param $from
     * @param $to
     * @param null $type
     * @return Message
     * @throws DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    public function getStatementOfAccount(Account $account, $from, $to, $type = null)
    {
        $this->assureDialog();
        
        $segment = $this->buildStatementSegment($type);
        
        return $this->newMessage()
            ->push($segment->fromAccount($account)->setFromDate($from)->setToDate($to))
            ->addSignature()
            ->mergeDialogParameters($this->dialogParameters)
            ->encrypt()
            ->prepare();
    }

    /**
     * @return Message
     * @throws DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    public function close()
    {
        return $this->newMessage(new HKEND())
            ->addSignature()
            ->mergeDialogParameters($this->dialogParameters)
            ->encrypt()
            ->prepare();
    }


    /**
     * @param $type
     * @return HKCAZ|HKKAZ
     */
    protected function buildStatementSegment($type)
    {
        if ($type == 'swift') {
            return $this->buildSwiftStatementSegment();
        }
        if ($type == 'camt') {
            return $this->buildCamtStatementSegment();
        }
        return $this->guessStatementSegment();
    }

    /**
     * @return HKKAZ
     */
    protected function buildSwiftStatementSegment()
    {
        return (new HKKAZ())->setVersion($this->dialogParameters->swiftStatementVersion);
    }

    /**
     * @return HKCAZ
     */
    protected function buildCamtStatementSegment()
    {
        return new HKCAZ();
    }

    /**
     * @return HKCAZ|HKKAZ
     */
    protected function guessStatementSegment()
    {
        return $this->dialogParameters->camtVersion ? $this->buildCamtStatementSegment() : $this->buildSwiftStatementSegment();
    }

    /**
     * @param array $segments
     * @return Message
     * @throws DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    protected function newMessage($segments = [])
    {
        if (!is_array($segments)) {
            $segments = [$segments];
        }
        
        $this->assureDialog();
        
        
        $result = $this->message->newMessage($this->credentials);
        foreach ($segments as $segment) {
            $result->push($segment);
        }
        return $result;
    }

    /**
     * @return $this
     * @throws DialogMissingException
     */
    protected function assureDialog()
    {
        if (!$this->credentials) {
            throw new DialogMissingException('Inject Dialog before usage');
        }
        return $this;
    }
}
