<?php

namespace Tests;

use Abiturma\PhpFints\BaseFints;
use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Models\Account;
use Abiturma\PhpFints\Models\IdentifiesBankAccount;
use Abiturma\PhpFints\Response\Messages\AccountsResponse;
use Abiturma\PhpFints\Response\Response;

/**
 * Class BaseFintsTest
 * @package Tests
 */
class BaseFintsTest extends TestCase
{
    protected $dialog;

    protected $credentials;
    
    protected $response;

    public function setUp(): void
    {
        parent::setup();
        $this->dialog = $this->createMock(Dialog::class);
        $this->credentials = $this->createMock(HoldsCredentials::class);
        $this->response = $this->createMock(Response::class);
    }

    /** @test */
    public function it_passes_a_get_accounts_request_to_the_dialog()
    {
        $this->dialog->expects($this->once())->method('sync');
        $this->dialog->expects($this->once())->method('init');
        $this->dialog->expects($this->once())->method('getAccounts')->willReturn($this->response);
        $this->dialog->expects($this->once())->method('close');
        $accountResponse = $this->createMock(AccountsResponse::class);
        $accountResponse->method('getAccounts')->willReturn([1,2,3]);
        $this->response->expects($this->once())->method('accounts')->willReturn($accountResponse);
        $this->assertEquals([1,2,3], $this->make()->getAccounts());
    }
    
    
    /** @test */
    public function it_gets_an_account_statment()
    {
        $this->dialog->expects($this->once())->method('sync');
        $this->dialog->expects($this->once())->method('init');
        $this->dialog->expects($this->once())->method('getStatementOfAccount')->willReturn([1,2,3]);
        $this->dialog->expects($this->once())->method('close');
        
        
        $account = $this->createMock(IdentifiesBankAccount::class);
        $account->method('getAccountAttributes')->willReturn([]);
        
        $this->assertEquals([1,2,3], $this->make()->getStatementOfAccount($account));
    }
    
    /** @test */
    public function it_gets_a_swift_statement()
    {
        $this->dialog->expects($this->once())
            ->method('getStatementOfAccount')
            ->with($this->isInstanceOf(IdentifiesBankAccount::class), $this->anything(), $this->anything(), $this->equalTo('swift'))
            ->willReturn([1,2,3]);

        $account = $this->createMock(IdentifiesBankAccount::class);
        $account->method('getAccountAttributes')->willReturn([]);

        $this->assertEquals([1,2,3], $this->make()->getSwiftStatementOfAccount($account));
    }


    /** @test */
    public function it_gets_a_camt_statement()
    {
        $this->dialog->expects($this->once())
            ->method('getStatementOfAccount')
            ->with($this->isInstanceOf(IdentifiesBankAccount::class), $this->anything(), $this->anything(), $this->equalTo('camt'))
            ->willReturn([1,2,3]);

        $account = $this->createMock(IdentifiesBankAccount::class);
        $account->method('getAccountAttributes')->willReturn([]);

        $this->assertEquals([1,2,3], $this->make()->getCamtStatementOfAccount($account));
    }
    
    /** @test */
    public function it_proxies_calls_to_credentials()
    {
        $this->credentials->expects($this->once())->method('setUsername')->with('testUsername');
        $this->make()->username('testUsername');
    }


    /**
     * @return BaseFints
     */
    protected function make()
    {
        return new BaseFints($this->credentials, $this->dialog);
    }
}
