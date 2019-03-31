<?php

namespace Abiturma\PhpFints\Message;


use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Exceptions\DialogMissingException;
use Abiturma\PhpFints\Models\Account;
use Abiturma\PhpFints\Segments\HKCAZ;
use Abiturma\PhpFints\Segments\HKEND;
use Abiturma\PhpFints\Segments\HKIDN;
use Abiturma\PhpFints\Segments\HKKAZ;
use Abiturma\PhpFints\Segments\HKSPA;
use Abiturma\PhpFints\Segments\HKSYN;
use Abiturma\PhpFints\Segments\HKVVB;

class MessageBuilder
{
    protected $message;
    
    protected $dialogParameters = null; 
    
    protected $credentials = null; 

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function fromDialog(Dialog $dialog)
    {
        $this->dialogParameters = $dialog->getDialogParameters();
        $this->credentials = $dialog->getCredentials();
        return $this;
    }
    

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

    public function getAccounts()
    {
        return $this->newMessage(new HKSPA)
            ->addSignature()
            ->mergeDialogParameters($this->dialogParameters)
            ->encrypt()
            ->prepare();
    }

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

    public function close()
    {
        return $this->newMessage(new HKEND())
            ->addSignature()
            ->mergeDialogParameters($this->dialogParameters)
            ->encrypt()
            ->prepare();
    }




    protected function buildStatementSegment($type)
    {
        if($type == 'swift')  {
            return $this->buildSwiftStatementSegment(); 
        }
        if($type == 'camt') {
            return $this->buildCamtStatementSegment(); 
        }
        return $this->guessStatementSegment(); 
    }

    protected function buildSwiftStatementSegment()
    {
        return (new HKKAZ())->setVersion($this->dialogParameters->swiftStatementVersion); 
    }

    protected function buildCamtStatementSegment()
    {
        return new HKCAZ(); 
    }

    protected function guessStatementSegment()
    {
        return $this->dialogParameters->camtVersion ? $this->buildCamtStatementSegment() : $this->buildSwiftStatementSegment(); 
    }

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

    protected function assureDialog()
    {
        if(!$this->credentials) {
            throw new DialogMissingException('Inject Dialog before usage');
        }
        return $this; 
    }
}
