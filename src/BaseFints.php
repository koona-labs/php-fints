<?php

namespace Abiturma\PhpFints;


use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Models\HasAccountStatement;
use DateInterval;
use DateTime;
use Psr\Log\LoggerInterface;



class BaseFints
{

    protected $credentials;
    
    
    protected $dialog;
    
    
    public function __construct(HoldsCredentials $credentials, Dialog $dialog)
    {
        $this->credentials = $credentials;
        $this->dialog = $dialog;
        $this->prepareDialog();
    }

    public function withLogger(LoggerInterface $logger)
    {
        $this->dialog->setLogger($logger); 
        return $this; 
    }

    public function useCredentials(HoldsCredentials $credentials)
    {
        $this->credentials = $credentials; 
        $this->prepareDialog(); 
        return $this; 
    }


    public function getAccounts()
    {
        $this->dialog->sync();
        $this->dialog->init();
        $response = $this->dialog->getAccounts();
        $this->dialog->close();
        $result = $response->accounts()->getAccounts(); 
        return $result;
    }

    public function getSwiftStatementOfAccount(HasAccountStatement $account, DateTime $from = null, DateTime $to = null)
    {
        return $this->getStatementOfAccountByType($account,$from,$to,'swift');    
    }

    public function getCamtStatementOfAccount(HasAccountStatement $account, DateTime $from = null, DateTime $to = null)
    {
        return $this->getStatementOfAccountByType($account,$from,$to,'camt');    
    }

    public function getStatementOfAccount(HasAccountStatement $account, DateTime $from = null, DateTime $to = null)
    {
        return $this->getStatementOfAccountByType($account,$from,$to);
    }

    public function getStatementOfAccountByType(HasAccountStatement $account, DateTime $from = null, DateTime $to = null, $type = null)
    {
        $from = $from ?? (new DateTime())->sub(new DateInterval('P6M'));
        $to = $to ?? (new DateTime());
        
        $this->dialog->sync();
        $this->dialog->init();
        
        $statements = $this->dialog->getStatementOfAccount($account->toFinTsAccount(), $from, $to, $type);
        
        $this->dialog->close();
        
        return $statements;
    }


    protected function prepareDialog()
    {
        return $this->dialog->setCredentials($this->credentials);
    }

    public function __call($method, $arguments)
    {
        $setter = 'set' . ucfirst($method);
        if (method_exists($this->credentials, $setter) && count($arguments) > 0) {
            $this->credentials->$setter($arguments[0]);
        }
        return $this;
    }


}