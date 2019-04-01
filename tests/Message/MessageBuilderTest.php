<?php

namespace Tests\Message;

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
use Abiturma\PhpFints\Segments\HKVVB;
use DateTime;
use Tests\TestCase;


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
        $this->message->method('addSignature')->will($this->returnSelf()); 
        $this->message->method('mergeDialogParameters')->will($this->returnSelf());
        $this->message->method('encrypt')->will($this->returnSelf()); 
        $this->message->method('prepare')->will($this->returnSelf()); 
        $this->message->method('push')->will($this->returnSelf()); 
        $this->message->method('newMessage')->will($this->returnSelf()); 
    }
    
    
    /** @test */
    public function it_throws_an_exception_upon_message_building_if_no_dialog_is_provided()
    {
        $this->expectException(DialogMissingException::class); 
        (new MessageBuilder($this->message))->sync(); 
        
    }
    
    /** @test */
    public function it_builds_a_sync_message()
    {
        $this->message
            ->expects($this->exactly(3))
            ->method('push')
            ->withConsecutive(
                [$this->isInstanceOf(HKIDN::class)],
                [$this->isInstanceOf(HKVVB::class)],
                [$this->isInstanceOf(HKSYN::class)]
            );
        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters); 
        $this->message->expects($this->once())->method('addSignature'); 
        $this->message->expects($this->once())->method('encrypt'); 
        $this->assertInstanceOf(Message::class,$this->make()->sync()); 
    }
    
    /** @test */
    public function it_builds_a_dialog_initialization_message()
    {
        $this->message
            ->expects($this->exactly(2))
            ->method('push')
            ->withConsecutive(
                [$this->isInstanceOf(HKIDN::class)],
                [$this->isInstanceOf(HKVVB::class)]
            );

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class,$this->make()->init());
    }
    
    
    /** @test */
    public function it_builds_a_get_accounts_message()
    {
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKSPA::class)); 

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class,$this->make()->getAccounts());
    }
    
    /** @test */
    public function it_builds_a_get_swift_statement_of_account_message()
    {
        
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKKAZ::class));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class,$this->getAccount('swift'));
    }

    /** @test */
    public function it_builds_a_get_camt_statement_of_account_message()
    {
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKCAZ::class));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class,$this->getAccount('camt'));
    }
    
    /** @test */
    public function it_guesses_the_right_account_type_if_a_camt_version_is_given()
    {
        $this->dialogParameters->method('__get')->with('camtVersion')->willReturn(7);
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKCAZ::class));

        $this->assertInstanceOf(Message::class,$this->getAccount());
    }


    /** @test */
    public function it_guesses_the_right_account_type_if_no_camt_version_is_given()
    {
        $this->dialogParameters->expects($this->at(0))->method('__get')->with('camtVersion')->willReturn(null);
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKKAZ::class));

        $this->assertInstanceOf(Message::class,$this->getAccount());
    }
    
    /** @test */
    public function it_closes_a_dialog()
    {
        $this->message
            ->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(HKEND::class));

        $this->message->expects($this->once())->method('mergeDialogParameters')->with($this->dialogParameters);
        $this->message->expects($this->once())->method('addSignature');
        $this->message->expects($this->once())->method('encrypt');
        $this->assertInstanceOf(Message::class,$this->make()->close());
    }
    
    


    protected function getAccount($type = null)
    {
        $account = $this->createMock(Account::class);
        $from = new DateTime();
        $to = new DateTime();
        return $this->make()->getStatementOfAccount($account,$from,$to,$type); 
    }
    
    

    protected function make()
    {
        return (new MessageBuilder($this->message))->fromDialog($this->dialog); 
    }
        
    
    

}

