<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Models\Account;
use DateTime;

class HKCAZ extends AbstractSegment
{

    const VERSION = 1;

    const NAME = 'HKCAZ';

    /*
     * Identification/Identifikation
     * DataFields: 
     * 2 Ktv (v6) or Kti (v7)
     * 3 CamtVersions
     * 4 All Accounts ? 
     * 5 FromDate
     * 6 ToDate
     * 7 MaxNumberOfEntries
     * 8 PaginationToken
     */


    protected function boot()
    {
        $this->addElement('')
            ->addElement('')
            ->addElement('N')
            ->addElement('')
            ->addElement('')
            ->addElement('')
            ->addElement('');

    }

    public function fromAccount(Account $account)
    {
        $this->setElementAtPosition(2, $account->toKti());
        return $this;
    }

    public function setCamtVersion($version)
    {
        return $this->setElementAtPosition(3,$version); 
    }
    
    public function setFromDate(DateTime $date)
    {
        return $this->setElementAtPosition(5, $date->format('Ymd'));
    }

    public function setToDate(DateTime $date)
    {
        return $this->setElementAtPosition(6, $date->format('Ymd'));
    }

    public function setPaginationToken($token)
    {
        return $this->setElementAtPosition(8,$token);
    }


    public function mergeDialogParameters(DialogParameters $parameters)
    {
        return $this->setPaginationToken($parameters->paginationToken)
            ->setCamtVersion($parameters->camtVersion);
    }


}