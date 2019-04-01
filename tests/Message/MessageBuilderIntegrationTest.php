<?php

namespace Tests\Message;

use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\Dialog;
use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Encryption\NullEncrypter;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Message\MessageBuilder;
use Tests\TestCase;

/**
 * Class MessageBuilderIntegrationTest
 * @package Tests\Message
 */
class MessageBuilderIntegrationTest extends TestCase
{
    protected $credentials;
    
    
    protected $dialog;
    
    protected $dialogParameters;
    

    public function setUp() :void
    {
        parent::setUp();
            
        $this->credentials = $this->createMock(HoldsCredentials::class);
        $this->credentials->method('username')->willReturn('myTestUsername');
        $this->credentials->method('bankCode')->willReturn(12345678);
        $this->credentials->method('pin')->willReturn('mySecretPin');
        
        $this->dialog = $this->createMock(Dialog::class);
        $this->dialogParameters = $this->createMock(DialogParameters::class);
        
        $this->dialog->method('getCredentials')->willReturn($this->credentials);
        $this->dialog->method('getDialogParameters')->willReturn($this->dialogParameters);
    }
    
    /** @test */
    public function it_builds_a_sync_message_which_is_a_message()
    {
        $this->assertInstanceOf(Message::class, $this->makeMessageBuilder()->sync());
    }
    
    /** @test */
    public function the_sync_message_has_the_right_sequence_of_segments()
    {
        $sync = $this->makeMessageBuilder()->sync()->toString();
        $regex = "/HNHBK.*HNVSK.*HNVSD.*@.*HNSHK.*HKIDN.*HKVVB.*HKSYN.*HNSHA.*HNHBS/";
        $this->assertRegExp($regex, $sync);
    }
    
    /** @test */
    public function the_sync_message_hast_the_right_credentials()
    {
        $sync = $this->makeMessageBuilder()->sync()->toString();
        $regex = "/HKIDN.*12345678.*myTestUsername.*HNSHA.*mySecretPin.*HNHBS/";
        $this->assertRegExp($regex, $sync);
    }
    
    /** @test */
    public function it_builds_a_dialog_initialization()
    {
        $init = $this->makeMessageBuilder()->sync()->toString();
        $regex = "/HKIDN:3.*HKVVB:4.*/";
        $this->assertRegExp($regex, $init);
    }
    
    
    
    /** @test */
    public function it_builds_a_close_message()
    {
        $close = $this->makeMessageBuilder()->close()->toString();
        $this->assertStringContainsString('HKEND', $close);
    }


    /**
     * @return MessageBuilder
     */
    protected function makeMessageBuilder()
    {
        $message = new Message(new NullEncrypter());
        $builder = new MessageBuilder($message);
        return $builder->fromDialog($this->dialog);
    }
}
