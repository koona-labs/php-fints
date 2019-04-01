<?php

namespace Abiturma\PhpFints\Dialog;

use Abiturma\PhpFints\Adapter\SendsMessages;
use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Message\MessageBuilder;
use Abiturma\PhpFints\Models\Account;
use Abiturma\PhpFints\Response\Response;
use DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class Dialog
 * @package Abiturma\PhpFints
 */
class Dialog
{


    /**
     * @var SendsMessages
     */
    protected $adapter;

    /**
     * @var MessageBuilder
     */
    protected $messageBuilder;

    /**
     * @var DialogParameters
     */
    protected $dialogParameters;

    /**
     * @var HoldsCredentials
     */
    protected $credentials;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Dialog constructor.
     * @param SendsMessages $adapter
     * @param MessageBuilder $messageBuilder
     * @param DialogParameters $dialogParameters
     * @param LoggerInterface $logger
     */
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

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return Response
     * @throws \Abiturma\PhpFints\Exceptions\DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
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

    /**
     * @return Response
     * @throws \Abiturma\PhpFints\Exceptions\DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
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

    /**
     * @return Response
     * @throws \Abiturma\PhpFints\Exceptions\DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    public function getAccounts()
    {
        $this->logger->info('[Prepare Get Accounts Message]');
        $response = $this->sendMessage($this->newMessage()->getAccounts());
        return $response;
    }

    /**
     * Returns all Transactions of the given account within the given period
     *
     * @param Account $account
     * @param DateTime $from
     * @param DateTime $to
     * @param $type
     * @return array
     * @throws \Abiturma\PhpFints\Exceptions\DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\NoStatementOfAccountResponse
     * @throws \Abiturma\PhpFints\Exceptions\UnexpectedResponseType
     * @throws \Genkgo\Camt\Exception\ReaderException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
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

    /**
     * @return Response
     * @throws \Abiturma\PhpFints\Exceptions\DialogMissingException
     * @throws \Abiturma\PhpFints\Exceptions\MessageHeadMissingException
     */
    public function close()
    {
        $this->logger->info('[Prepare End Dialog Message]');
        $response = $this->sendMessage($this->newMessage()->close());
        if ($response->isOk()) {
            $this->dialogParameters->reset();
        }
        return $response;
    }

    /**
     * @return DialogParameters
     */
    public function getDialogParameters()
    {
        return $this->dialogParameters;
    }

    /**
     * @return HoldsCredentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param HoldsCredentials $credentials
     * @return $this
     */
    public function setCredentials(HoldsCredentials $credentials)
    {
        $this->credentials = $credentials;
        return $this;
    }

    /**
     * @return MessageBuilder
     */
    protected function newMessage()
    {
        return $this->messageBuilder->fromDialog($this);
    }

    /**
     * @param Message $message
     * @return Response
     */
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
