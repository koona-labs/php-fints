<?php

namespace Abiturma\PhpFints\Response\Messages;


use Abiturma\PhpFints\DataElements\Groups\Ktz;
use Abiturma\PhpFints\Models\Account;

class AccountsResponse extends AbstractResponseMessage
{

    public function isSuccess()
    {
        return !!$this->response->getFirstOfType('HISPA');  
    }

    public function getAccounts()
    {
        if(!$this->isSuccess()) {
            return [];     
        }
        
        
        $hispa = $this->getFirstOfType('HISPA'); 
        $accounts = $hispa->getElements(); 
        if(!is_array($accounts)) {
            return []; 
        }
        return array_map(function($account) {
            $accountField = Ktz::fromDataElementGroup($account); 
            return Account::fromKtz($accountField);     
        }, $accounts); 
        
        
    }
    
}