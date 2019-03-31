<?php

namespace Tests\Dialog;

use Abiturma\PhpFints\Adapter\SendsMessages;
use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Message\MessageBuilder;
use Abiturma\PhpFints\Models\Account;
use Abiturma\PhpFints\Response\HoldsDialogParameters;
use Abiturma\PhpFints\Response\Messages\StatementOfAccount;
use Abiturma\PhpFints\Response\Response;
use DateTime;
use Psr\Log\LoggerInterface;
use Tests\TestCase;


class DialogTest extends TestCase
{

    protected $adapter; 
    
    protected $messageBuilder; 
    
    protected $dialogParameters; 
    
    protected $logger; 
    
    protected $message; 
    
    protected $response; 
    
    public function setUp(): void
    {
        parent::setup();
        
        $this->response = $this->createMock(Response::class); 
        $this->adapter = $this->createMock(SendsMessages::class);
        $this->adapter->method('to')->will($this->returnSelf()); 
        $this->adapter->method('send')->willReturn($this->response); 
        $this->messageBuilder = $this->createMock(MessageBuilder::class); 
        $this->dialogParameters = $this->createMock(DialogParameters::class);
        $this->logger = $this->createMock(LoggerInterface::class); 
        $this->message = $this->createMock(Message::class); 
        $this->messageBuilder->method('fromDialog')->will($this->returnSelf());
    }


    /** @test */
    public function it_sends_a_sync_message()
    {
        $this->messageBuilder->expects($this->once())->method('sync')->willReturn($this->message); 
        $this->assertInstanceOf(Response::class,$this->make()->sync()); 
    }
    
    /** @test */
    public function if_the_sync_response_is_ok_dialog_parameters_are_merged()
    {
        $this->response->method('isOk')->willReturn(true); 
        $this->response->method('sync')->willReturn($this->createMock(HoldsDialogParameters::class)); 
        $this->messageBuilder->method('sync')->willReturn($this->message);
        $this->dialogParameters->expects($this->once())->method('mergeResponseOnlyWith'); 
        $this->make()->sync(); 
    }
    
    
    /** @test */
    public function it_initializes_a_dialog()
    {
        $this->messageBuilder->expects($this->once())->method('init')->willReturn($this->message);
        $this->assertInstanceOf(Response::class,$this->make()->init());
    }
    
    /** @test */
    public function if_the_init_response_is_ok_the_parameters_are_merged_and_the_message_number_is_incremented()
    {
        $this->response->method('isOk')->willReturn(true);
        $this->messageBuilder->method('init')->willReturn($this->message);
        $this->dialogParameters->expects($this->once())->method('mergeResponse');
        $this->dialogParameters->expects($this->once())->method('incrementMessageNumber'); 
        $this->make()->init();
    }
    
    
    /** @test */
    public function it_sends_an_accounts_query()
    {
        $this->messageBuilder->expects($this->once())->method('getAccounts')->willReturn($this->message);
        $this->assertInstanceOf(Response::class,$this->make()->getAccounts());
    }
    
    /** @test */
    public function it_sends_a_loop_of_get_statement_messages()
    {
        $statement = $this->createMock(StatementOfAccount::class); 
        $statement->method('getTransactions')->willReturn([1]); 
        $this->response->method('statementOfAccount')->willReturn($statement); 
        $this->dialogParameters->method('incrementMessageNumber')->will($this->returnSelf()); 
        
        $account = $this->createMock(Account::class); 
        $from = new DateTime(); 
        $to = new DateTime(); 
        
        $this->messageBuilder->expects($this->once())->method('getStatementOfAccount')->willReturn($this->message);
        $this->assertEquals([1],$this->make()->getStatementOfAccount($account,$from,$to,'auto'));
    }
    
    /** @test */
    public function if_the_get_statement_result_is_paginated_it_will_merge_the_transactions()
    {
        $statement = $this->createMock(StatementOfAccount::class);
        $statement->method('isPaginated')->will($this->onConsecutiveCalls(true,true,false)); 
        $statement->method('getTransactions')->will($this->onConsecutiveCalls([1],[2],[3]));
        $this->response->method('statementOfAccount')->willReturn($statement);
        $this->dialogParameters->method('incrementMessageNumber')->will($this->returnSelf());
        $this->dialogParameters->expects($this->any())->method('__call')->with('setPaginationToken',[null]); 
        
        $account = $this->createMock(Account::class);
        $from = new DateTime();
        $to = new DateTime();

        $this->messageBuilder->expects($this->exactly(3))->method('getStatementOfAccount')->willReturn($this->message);
        $this->assertEquals([1,2,3],$this->make()->getStatementOfAccount($account,$from,$to,'auto'));
    }
    
    
    /** @test */
    public function it_closes_a_dialog()
    {
        $this->messageBuilder->expects($this->once())->method('close')->willReturn($this->message);
        $this->assertInstanceOf(Response::class,$this->make()->close());
    }
    
    /** @test */
    public function after_a_dialog_is_closed_the_dialog_paramters_are_reset()
    {
        $this->messageBuilder->method('close')->willReturn($this->message);
        $this->response->expects($this->once())->method('isOk')->willReturn(true); 
        $this->dialogParameters->expects($this->once())->method('reset');
        $this->make()->close(); 
    }
    
    
    
    
    
    
    
    
    protected function make()
    {
        return 
            (new Dialog($this->adapter,$this->messageBuilder,$this->dialogParameters,$this->logger))
            ->setCredentials($this->createMock(HoldsCredentials::class)); 
    }
    
    

}

