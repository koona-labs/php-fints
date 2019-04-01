<?php

namespace Abiturma\PhpFints\Segments;

use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Models\Account;
use DateTime;

/**
 * Get Camt Statement
 *
 * Fields
 * - 2 Ktv (v6) or Kti (v7)
 * - 3 CamtVersions
 * - 4 Query for all Accounts ?
 * - 5 FromDate
 * - 6 ToDate
 * - 7 MaxNumberOfEntries
 * - 8 PaginationToken
 *
 * @package Abiturma\PhpFints
 */
class HKCAZ extends AbstractSegment
{
    const VERSION = 1;

    const NAME = 'HKCAZ';


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

    /**
     * @param Account $account
     * @return $this
     */
    public function fromAccount(Account $account)
    {
        $this->setElementAtPosition(2, $account->toKti());
        return $this;
    }

    /**
     * @param $version
     * @return HKCAZ
     */
    public function setCamtVersion($version)
    {
        return $this->setElementAtPosition(3, $version);
    }

    /**
     * @param DateTime $date
     * @return HKCAZ
     */
    public function setFromDate(DateTime $date)
    {
        return $this->setElementAtPosition(5, $date->format('Ymd'));
    }

    /**
     * @param DateTime $date
     * @return HKCAZ
     */
    public function setToDate(DateTime $date)
    {
        return $this->setElementAtPosition(6, $date->format('Ymd'));
    }

    /**
     * @param $token
     * @return HKCAZ
     */
    public function setPaginationToken($token)
    {
        return $this->setElementAtPosition(8, $token);
    }


    /**
     * @param DialogParameters $parameters
     * @return AbstractSegment|HKCAZ
     */
    public function mergeDialogParameters(DialogParameters $parameters)
    {
        return $this->setPaginationToken($parameters->paginationToken)
            ->setCamtVersion($parameters->camtVersion);
    }
}
