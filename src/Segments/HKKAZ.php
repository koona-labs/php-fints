<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Models\Account;
use DateTime;

class HKKAZ extends AbstractSegment
{

    const VERSION = 6;

    const NAME = 'HKKAZ';

    protected $version = 6;

    /*
     * Identification/Identifikation
     * DataFields: 
     * 2 Ktv (v6) or Kti (v7)
     * 3 All Accounts ? 
     * 4 FromDate
     * 5 ToDate
     * 6 MaxNumberOfEntries
     * 7 PaginationToken
     */


    protected function boot()
    {
        $this->addElement('')
            ->addElement('N')
            ->addElement((new DateTime())->format('Ymd'))
            ->addElement((new DateTime())->format('Ymd'))
            ->addElement('')
            ->addElement(''); 
                
    }

    public function setFromDate(DateTime $date)
    {
        return $this->setElementAtPosition(4, $date->format('Ymd'));     
    }

    public function setToDate(DateTime $date)
    {
        return $this->setElementAtPosition(5, $date->format('Ymd'));     
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function fromAccount(Account $account)
    {
        $ktx = $this->version == 7 ? $account->toKti() : $account->toKtv();
        $this->setElementAtPosition(2,$ktx);
        return $this; 
    }

    public function setPaginationToken($token)
    {
        $this->setElementAtPosition(7,$token); 
    }

    public function mergeDialogParameters(DialogParameters $dialogParameters)
    {
        return $this->setPaginationToken($dialogParameters->paginationToken); 
    }


}