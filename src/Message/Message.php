<?php

namespace Abiturma\PhpFints\Message;


use Abiturma\PhpFints\Credentials\ConfigCredentials;
use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Encryption\EncryptsASequenceOfSegments;
use Abiturma\PhpFints\Exceptions\MessageHeadMissingException;
use Abiturma\PhpFints\Segments\AbstractSegment;
use Abiturma\PhpFints\Segments\HNHBK;
use Abiturma\PhpFints\Segments\HNHBS;
use Abiturma\PhpFints\Segments\HNSHA;
use Abiturma\PhpFints\Segments\HNSHK;

class Message
{

    protected $credentials;

    /**
     * @var array
     */
    protected $segments = [];
    
    protected $unencryptedSegments = []; 

    protected $envelope = [];

    protected $encrypter;


    public function __construct(EncryptsASequenceOfSegments $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function newMessage(HoldsCredentials $credentials)
    {
        $this->credentials = $credentials;
        $this->segments = [];
        $this->unencryptedSegments = []; 
        $this->buildEnvelope();
        return $this;
    }

    public function prepend(AbstractSegment $segment)
    {
        $this->checkEnvelope();
        $segment->setSegmentNumber(2);
        $this->shiftSegmentNumbers();
        $this->segments = array_merge([$segment], $this->segments);
        $this->envelope[1]->incrementSegmentNumber();
        return $this;
    }

    public function push(AbstractSegment $segment)
    {
        $this->checkEnvelope();
        $segment->setSegmentNumber(count($this->segments) + 2); //1 for the message head 1 for the increment 
        $this->segments[] = $segment;
        $this->envelope[1]->incrementSegmentNumber();
        return $this;
    }

    public function addSignature()
    {
        $this->checkEnvelope();
        $signatureHead = (new HNSHK())
            ->setBankCode($this->credentials->bankCode())
            ->setUsername($this->credentials->username());
        $endOfSignature = (new HNSHA())->setPin($this->credentials->pin())->setSecurityControlReference($signatureHead);
        $this->prepend($signatureHead)->push($endOfSignature);
        return $this;
    }

    public function mergeDialogParameters(DialogParameters $parameters = null)
    {
        $this->checkEnvelope();
        if (!$parameters) {
            return $this;
        }
        $this->envelope[0]->mergeDialogParameters($parameters);
        $this->envelope[1]->mergeDialogParameters($parameters);
        foreach ($this->segments as $segment) {
            $segment->mergeDialogParameters($parameters);
        }
        return $this;
    }

    public function encrypt()
    {
        $this->unencryptedSegments = $this->segments; 
        $this->segments = $this->encrypter->encrypt($this->segments);
        return $this;

    }

    public function prepare()
    {
        $this->checkEnvelope();
        if($this->unencryptedSegments) {
            $this->unencryptedSegments = $this->wrapWithEnvelope($this->unencryptedSegments);    
        }
        $this->segments = $this->wrapWithEnvelope($this->segments); 
        
        $this->envelope = [];
        $length = mb_strlen($this->toString());
        $this->segments[0]->setMessageLength($length);
        return $this;
    }

    public function getSegments()
    {
        return $this->segments;
    }

    public function getSegmentOrder()
    {
        $result = []; 
        foreach($this->unencryptedSegments as $segment) {
            $result[$segment->getSegmentNumber()] = $segment->getName(); 
        }
        return $result; 
    }

    public function toString()
    {
        $result = array_map(function ($segment) {
            return $segment->toString();
        }, $this->segments);
        return implode($result, '');
    }

    public function toBase64()
    {
        return base64_encode($this->toString());
    }


    protected function buildEnvelope()
    {
        $head = (new HNHBK());
        $end = (new HNHBS())->setSegmentNumber(2);
        $this->envelope = [$head, $end];
    }

    protected function checkEnvelope()
    {
        if (count($this->envelope) != 2) {
            throw new MessageHeadMissingException();
        }
    }

    protected function shiftSegmentNumbers()
    {
        foreach ($this->segments as $segment) {
            $segment->incrementSegmentNumber();
        }
    }

    protected function wrapWithEnvelope(array $segments)
    {
        return array_merge([$this->envelope[0]], $segments, [$this->envelope[1]]);
    }


}