<?php

namespace Abiturma\PhpFints\Segments;

use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Models\Account;
use DateTime;

/**
 * Get Swift Statement
 *
 * Fields
 * - 2 Ktv (v6) or Kti (v7)
 * - 3 Query for All Accounts ?
 * - 4 FromDate
 * - 5 ToDate
 * - 6 MaxNumberOfEntries
 * - 7 PaginationToken
 *
 * @package Abiturma\PhpFints
 */
class HKKAZ extends AbstractSegment
{
    const VERSION = 6;

    const NAME = 'HKKAZ';

    protected $version = 6;
    

    protected function boot()
    {
        $this->addElement('')
            ->addElement('N')
            ->addElement((new DateTime())->format('Ymd'))
            ->addElement((new DateTime())->format('Ymd'))
            ->addElement('')
            ->addElement('');
    }

    /**
     * @param DateTime $date
     * @return HKKAZ
     */
    public function setFromDate(DateTime $date)
    {
        return $this->setElementAtPosition(4, $date->format('Ymd'));
    }

    /**
     * @param DateTime $date
     * @return HKKAZ
     */
    public function setToDate(DateTime $date)
    {
        return $this->setElementAtPosition(5, $date->format('Ymd'));
    }

    /**
     * @param $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param Account $account
     * @return $this
     */
    public function fromAccount(Account $account)
    {
        $ktx = $this->version == 7 ? $account->toKti() : $account->toKtv();
        $this->setElementAtPosition(2, $ktx);
        return $this;
    }

    /**
     * @param $token
     */
    public function setPaginationToken($token)
    {
        $this->setElementAtPosition(7, $token);
    }

    /**
     * @param DialogParameters $dialogParameters
     * @return AbstractSegment
     */
    public function mergeDialogParameters(DialogParameters $dialogParameters)
    {
        return $this->setPaginationToken($dialogParameters->paginationToken);
    }
}
