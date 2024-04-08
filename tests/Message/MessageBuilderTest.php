<?php

namespace Abiturma\PhpFints\Tests\Message;

use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Exceptions\DialogMissingException;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Message\MessageBuilder;
use Abiturma\PhpFints\Models\Account;
use Abiturma\PhpFints\Segments\HKCAZ;
use Abiturma\PhpFints\Segments\HKEND;
use Abiturma\PhpFints\Segments\HKIDN;
use Abiturma\PhpFints\Segments\HKKAZ;
use Abiturma\PhpFints\Segments\HKSPA;
use Abiturma\PhpFints\Segments\HKSYN;
use Abiturma\PhpFints\Segments\HKTAN;
use Abiturma\PhpFints\Segments\HKVVB;
use DateTime;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class MessageBuilderTest
 * @package Tests\Message
 */
class MessageBuilderTest extends TestCase
{
    protected $credentials;

    protected $dialogParameters;

    protected $dialog;

    protected $message;

    public function setUp(): void
    {
        parent::setup();
        $this->dialogParameters = $this->createMock(DialogParameters::class);
        $this->credentials = $this->createMock(HoldsCredentials::class);
        $this->dialog = $this->createMock(Dialog::class);
        $this->dialog->method('getCredentials')->willReturn($this->credentials);
        $this->dialog->method('getDialogParameters')->willReturn($this->dialogParameters);
        $this->message = $this->createMock(Message::class);
        $this->message->method('addSignature')->willReturnSelf();
        $this->message->method('mergeDialogParameters')->willReturnSelf();
        $this->message->method('encrypt')->willReturnSelf();
        $this->message->method('prepare')->willReturnSelf();
        $this->message->method('push')->willReturnSelf();
        $this->message->method('newMessage')->willReturnSelf();
    }


    #[Test]
    public function it_throws_an_exception_upon_message_building_if_no_dialog_is_provided()
    {
        $this->expectException(DialogMissingException::class);
        (new MessageBuilder($this->message))->sync();
    }

    #[Test]
    public function it_builds_a_sync_message()
    {
        $this->message
            ->expects($this->exactly(3))
            ->method('push')
            ->with(...self::withConsecutive(
                [$this->isInstanceOf(HKIDN::class)],
                [$this->isInstanceOf(HKVVB::class)],
                [$this->isInstanceOf(HKSYN::class)]
            ));
        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class, $this->make()->sync());
    }

    #[Test]
    public function it_builds_a_dialog_initialization_message()
    {
        $this->message
            ->expects($this->exactly(3))
            ->method('push')
            ->with(...self::withConsecutive(
                [$this->isInstanceOf(HKIDN::class)],
                [$this->isInstanceOf(HKVVB::class)],
                [$this->isInstanceOf(HKTAN::class)]
            ));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class, $this->make()->init());
    }


    #[Test]
    public function it_builds_a_get_accounts_message()
    {
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKSPA::class));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class, $this->make()->getAccounts());
    }

    #[Test]
    public function it_builds_a_get_swift_statement_of_account_message()
    {
        $this->message
            ->expects($this->exactly(2))
            ->method('push')
            ->with(...self::withConsecutive(
                [$this->isInstanceOf(HKKAZ::class)],
                [$this->isInstanceOf(HKTAN::class)]
            ));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class, $this->getAccount('swift'));
    }

    #[Test]
    public function it_builds_a_get_camt_statement_of_account_message()
    {
        $this->message
            ->expects($this->exactly(2))
            ->method('push')
            ->with(...self::withConsecutive(
                [$this->isInstanceOf(HKCAZ::class)],
                [$this->isInstanceOf(HKTAN::class)]
            ));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class, $this->getAccount('camt'));
    }

    #[Test]
    public function it_guesses_the_right_account_type_if_a_camt_version_is_given()
    {
        $this->dialogParameters->method('__get')->with('camtVersion')->willReturn(7);
        $counter = 0; 
        $this->message
            ->expects($this->exactly(2))
            ->method('push')
            ->with( $this->callback(function($param) use (&$counter) {
                $expected = [HKCAZ::class, HKTAN::class];
                return $param instanceof $expected[$counter++]; 
            }));
        $this->assertInstanceOf(Message::class, $this->getAccount());
    }


    #[Test]
    public function it_guesses_the_right_account_type_if_no_camt_version_is_given()
    {
        $this->dialogParameters->expects($this->exactly(2))
            ->method('__get')
            ->with(...self::withConsecutive(['camtVersion'], ['swiftStatementVersion']))
            ->willReturn(null);
        $counter = 0; 
        $this->message
            ->expects($this->exactly(2))
            ->method('push')
            ->with( $this->callback(function($param) use (&$counter) {
                $expected = [HKKAZ::class, HKTAN::class];
                return $param instanceof $expected[$counter++];
            }));
        $this->assertInstanceOf(Message::class, $this->getAccount());
    }

    #[Test]
    public function it_closes_a_dialog()
    {
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKEND::class));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class, $this->make()->close());
    }


    /**
     * @param null $type
     * @return Message
     * @throws DialogMissingException
     * @throws \ReflectionException
     */
    protected function getAccount($type = null)
    {
        $account = $this->createMock(Account::class);
        $from = new DateTime();
        $to = new DateTime();
        return $this->make()->getStatementOfAccount($account, $from, $to, $type);
    }


    /**
     * @return MessageBuilder
     */
    protected function make()
    {
        return (new MessageBuilder($this->message))->fromDialog($this->dialog);
    }
}
