<?php

namespace Tests\Message;

use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Encryption\NullEncrypter;
use Abiturma\PhpFints\Message\Message;
use Abiturma\PhpFints\Segments\AbstractSegment;
use Abiturma\PhpFints\Segments\HKKAZ;
use Abiturma\PhpFints\Segments\HKSYN;
use Abiturma\PhpFints\Segments\HNVSD;
use Abiturma\PhpFints\Segments\HNVSK;
use Tests\TestCase;


/**
 * Class MessageTest
 * @package Tests\Message
 */
class MessageTest extends TestCase
{

    protected $credentials;
    
    protected $encrypter; 

    public function setUp(): void
    {
        parent::setup();
      
        $this->encrypter = $this->createMock(NullEncrypter::class);;
        $this->credentials = $this->createMock(HoldsCredentials::class);
        $this->credentials->method('pin')->willReturn('mySecretPin');
        $this->credentials->method('username')->willReturn('myUsername');
        $this->credentials->method('bankCode')->willReturn(12345678);

    }

    /** @test */
    public function if_no_segments_are_provided_it_returns_an_empty_string()
    {
        $this->assertEquals('', $this->make()->toString());
    }

    /** @test */
    public function once_credentials_are_provided_it_returns_a_message_wrapper()
    {

        $this->assertEquals(
            "HNHBK:1:3+000000000043+300+0+1'HNHBS:2:1+1'",
            $this->make()->newMessage($this->credentials)->prepare()->toString()
        );
    }

    /** @test */
    public function it_handles_message_numbers_after_push()
    {
        $firstSegment = $this->getMockForAbstractClass(AbstractSegment::class);
        $secondSegment = $this->getMockForAbstractClass(AbstractSegment::class);
        $message = $this->make()->newMessage($this->credentials)
            ->push($firstSegment)
            ->push($secondSegment)
            ->prepare();
        
        $segmentsNumbers = array_map(function ($segment) {
            return $segment->getSegmentnumber(); 
        }, $message->getSegments());
        
        $this->assertEquals([1,2,3,4],$segmentsNumbers); 

    }

    /** @test */
    public function it_handles_message_numbers_after_prepend()
    {
        $firstSegment = $this->getMockForAbstractClass(AbstractSegment::class);
        $secondSegment = $this->getMockForAbstractClass(AbstractSegment::class);
        $message = $this->make()->newMessage($this->credentials)
            ->prepend($secondSegment)
            ->prepend($firstSegment)
            ->prepare();

        $segmentsNumbers = array_map(function ($segment) {
            return $segment->getSegmentnumber();
        }, $message->getSegments());

        $this->assertEquals([1,2,3,4],$segmentsNumbers);

    }

    /** @test */
    public function it_wraps_a_signature_around_a_message()
    {
        $message = $this->make()->newMessage($this->credentials)->addSignature();
        $this->assertStringStartsWith("HNSHK:2:4+PIN:1+999", $message->toString());
        $this->assertStringEndsWith("++mySecretPin'", $message->toString());
        $this->assertStringContainsString("280:12345678:myUsername:S:0:0'HNSHA:3:2+",$message->toString()); 
    }
    
    /** @test */
    public function it_encrypts_a_given_message()
    {
        //*Message Numbers are incorrect, since this is just a "unit test" 
        $this->encrypter->method('encrypt')->willReturn([new HNVSK(),(new HNVSD())->setEncryptedData("test'")]); 
        $message = $this->make()->newMessage($this->credentials)->encrypt()->toString(); 
        $this->assertStringContainsString("HNVSD:1:1+@5@test''",$message); 
    }
    
    /** @test */
    public function it_conserves_the_message_order()
    {
        $this->encrypter->method('encrypt')->willReturn([new HNVSK(),(new HNVSD())->setEncryptedData("test'")]);
        $message = $this->make()->newMessage($this->credentials)->push(new HKSYN())->push(new HKKAZ())->encrypt()->prepare();
        $this->assertEquals([1 => 'HNHBK', 2 => 'HKSYN', 3 => 'HKKAZ', 4 => 'HNHBS'],$message->getSegmentOrder());
    }


    /**
     * @return Message
     */
    public function make()
    {
        return new Message($this->encrypter);
    }

}

