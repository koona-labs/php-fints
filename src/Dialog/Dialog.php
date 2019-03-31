<?php

namespace Abiturma\PhpFints\Dialog;


use Abiturma\PhpFints\Adapter\SendsMessages;
use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Message\MessageBuilder;
use Abiturma\PhpFints\Models\Account;
use DateTime;
use Psr\Log\LoggerInterface;

class Dialog
{


    /**
     * @var SendsMessages
     */
    protected $adapter;

    protected $messageBuilder;

    protected $dialogParameters;

    protected $credentials;

    protected $logger;

    public function __construct(
        SendsMessages $adapter,
        MessageBuilder $messageBuilder,
        DialogParameters $dialogParameters,
        LoggerInterface $logger
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->adapter = $adapter;
        $this->dialogParameters = $dialogParameters;
        $this->logger = $logger;
    }

    public function sync()
    {
        $this->logger->info('[Prepare Sync Message]');
        $response = $this->sendMessage($this->newMessage()->sync());
        if ($response->isOk()) {
            $this->dialogParameters->mergeResponseOnlyWith($response->sync(), [
                'updVersion',
                'bpdVersion',
                'camtVersion',
                'systemId',
                'swiftStatementVersion',
                'tanFunctionCode'
            ]);
        }
        return $response;
    }

    public function init()
    {
        $this->logger->info('[Prepare Init Message]');
        $response = $this->sendMessage($this->newMessage()->init());
        if ($response->isOk()) {
            $this->dialogParameters->mergeResponse($response);
        }
        $this->dialogParameters->incrementMessageNumber();
        return $response;
    }

    public function getAccounts()
    {
        $this->logger->info('[Prepare Get Accounts Message]');
        $response = $this->sendMessage($this->newMessage()->getAccounts());
        return $response;

    }

    public function getStatementOfAccount(Account $account, DateTime $from, DateTime $to, $type)
    {
        $result = [];
        do {
            $this->logger->info('[Prepare Get Statement Of Account Message]');
            $response = $this->sendMessage($this->newMessage()->getStatementOfAccount($account, $from, $to, $type))
                ->statementOfAccount();
            $result = array_merge($result, $response->getTransactions());
            $this->dialogParameters
                ->incrementMessageNumber()
                ->setPaginationToken($response->getPaginationToken());
        } while ($response->isPaginated());

        $this->dialogParameters->setPaginationToken(null);

        return $result;
    }

    public function close()
    {
        $this->logger->info('[Prepare End Dialog Message]');
        $response = $this->sendMessage($this->newMessage()->close());
        if ($response->isOk()) {
            $this->dialogParameters->reset();
        }
        return $response;
    }

    public function getDialogParameters()
    {
        return $this->dialogParameters;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setCredentials(HoldsCredentials $credentials)
    {
        $this->credentials = $credentials;
        return $this;
    }

    protected function newMessage()
    {
        return $this->messageBuilder->fromDialog($this);
    }

    protected function sendMessage(Message $message)
    {
        $this->logger->info('[Sending message]');
        $this->logger->debug('[Message] ' . $message->toString());
        $response = $this->adapter->to($this->credentials->host())->send($message);
        $this->logger->info('[Received response]');
        $this->logger->info('[Feedback] ' . $response->getFullErrorMessage());
        $this->logger->debug('[Raw Response] ' . $response->getRaw());
        return $response;
    }

}