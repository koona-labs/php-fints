<?php

namespace Abiturma\PhpFints;

use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Models\HasAccountStatement;
use DateInterval;
use DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class BaseFints
 * @package Abiturma\PhpFints
 */
class BaseFints
{

    /**
     * @var HoldsCredentials
     */
    protected $credentials;

    /**
     * @var Dialog
     */
    protected $dialog;


    /**
     * BaseFints constructor.
     * @param HoldsCredentials $credentials
     * @param Dialog $dialog
     */
    public function __construct(HoldsCredentials $credentials, Dialog $dialog)
    {
        $this->credentials = $credentials;
        $this->dialog = $dialog;
        $this->prepareDialog();
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function withLogger(LoggerInterface $logger)
    {
        $this->dialog->setLogger($logger);
        return $this;
    }

    /**
     * @param HoldsCredentials $credentials
     * @return $this
     */
    public function useCredentials(HoldsCredentials $credentials)
    {
        $this->credentials = $credentials;
        $this->prepareDialog();
        return $this;
    }


    /**
     * @return array
     * @throws Exceptions\DialogMissingException
     * @throws Exceptions\MessageHeadMissingException
     */
    public function getAccounts()
    {
        $this->dialog->sync();
        $this->dialog->init();
        $response = $this->dialog->getAccounts();
        $this->dialog->close();
        $result = $response->accounts()->getAccounts();
        return $result;
    }

    /**
     * @param HasAccountStatement $account
     * @param DateTime|null $from
     * @param DateTime|null $to
     * @return array
     * @throws \Exception
     */
    public function getSwiftStatementOfAccount(HasAccountStatement $account, DateTime $from = null, DateTime $to = null)
    {
        return $this->getStatementOfAccountByType($account, $from, $to, 'swift');
    }

    /**
     * @param HasAccountStatement $account
     * @param DateTime|null $from
     * @param DateTime|null $to
     * @return array
     * @throws \Exception
     */
    public function getCamtStatementOfAccount(HasAccountStatement $account, DateTime $from = null, DateTime $to = null)
    {
        return $this->getStatementOfAccountByType($account, $from, $to, 'camt');
    }

    /**
     * @param HasAccountStatement $account
     * @param DateTime|null $from
     * @param DateTime|null $to
     * @return array
     * @throws \Exception
     */
    public function getStatementOfAccount(HasAccountStatement $account, DateTime $from = null, DateTime $to = null)
    {
        return $this->getStatementOfAccountByType($account, $from, $to);
    }

    /**
     * @param HasAccountStatement $account
     * @param DateTime|null $from
     * @param DateTime|null $to
     * @param null $type
     * @return array
     * @throws \Exception
     */
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


    /**
     * @return Dialog
     */
    protected function prepareDialog()
    {
        return $this->dialog->setCredentials($this->credentials);
    }

    /**
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $setter = 'set' . ucfirst($method);
        if (method_exists($this->credentials, $setter) && count($arguments) > 0) {
            $this->credentials->$setter($arguments[0]);
        }
        return $this;
    }
}
