<?php

namespace Abiturma\PhpFints\Message;

use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Encryption\EncryptsASequenceOfSegments;
use Abiturma\PhpFints\Exceptions\MessageHeadMissingException;
use Abiturma\PhpFints\Segments\AbstractSegment;
use Abiturma\PhpFints\Segments\HNHBK;
use Abiturma\PhpFints\Segments\HNHBS;
use Abiturma\PhpFints\Segments\HNSHA;
use Abiturma\PhpFints\Segments\HNSHK;

/**
 * Class Message
 * @package Abiturma\PhpFints
 */
class Message
{

    /**
     * @var HoldsCredentials
     */
    protected $credentials;

    /**
     * @var array
     */
    protected $segments = [];

    /**
     * @var array
     */
    protected $unencryptedSegments = [];

    /**
     * @var array
     */
    protected $envelope = [];

    /**
     * @var EncryptsASequenceOfSegments
     */
    protected $encrypter;


    /**
     * Message constructor.
     * @param EncryptsASequenceOfSegments $encrypter
     */
    public function __construct(EncryptsASequenceOfSegments $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * @param HoldsCredentials $credentials
     * @return $this
     */
    public function newMessage(HoldsCredentials $credentials)
    {
        $this->credentials = $credentials;
        $this->segments = [];
        $this->unencryptedSegments = [];
        $this->buildEnvelope();
        return $this;
    }

    /**
     * @param AbstractSegment $segment
     * @return $this
     * @throws MessageHeadMissingException
     */
    public function prepend(AbstractSegment $segment)
    {
        $this->checkEnvelope();
        $segment->setSegmentNumber(2);
        $this->shiftSegmentNumbers();
        $this->segments = array_merge([$segment], $this->segments);
        $this->envelope[1]->incrementSegmentNumber();
        return $this;
    }

    /**
     * @param AbstractSegment $segment
     * @return $this
     * @throws MessageHeadMissingException
     */
    public function push(AbstractSegment $segment)
    {
        $this->checkEnvelope();
        $segment->setSegmentNumber(count($this->segments) + 2); //1 for the message head 1 for the increment
        $this->segments[] = $segment;
        $this->envelope[1]->incrementSegmentNumber();
        return $this;
    }

    /**
     * @return $this
     * @throws MessageHeadMissingException
     */
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

    /**
     * @param DialogParameters|null $parameters
     * @return $this
     * @throws MessageHeadMissingException
     */
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

    /**
     * @return $this
     */
    public function encrypt()
    {
        $this->unencryptedSegments = $this->segments;
        $this->segments = $this->encrypter->encrypt($this->segments);
        return $this;
    }

    /**
     * @return $this
     * @throws MessageHeadMissingException
     */
    public function prepare()
    {
        $this->checkEnvelope();
        if ($this->unencryptedSegments) {
            $this->unencryptedSegments = $this->wrapWithEnvelope($this->unencryptedSegments);
        }
        $this->segments = $this->wrapWithEnvelope($this->segments);
        
        $this->envelope = [];
        $length = mb_strlen($this->toString());
        $this->segments[0]->setMessageLength($length);
        return $this;
    }

    /**
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @return array
     */
    public function getSegmentOrder()
    {
        $result = [];
        foreach ($this->unencryptedSegments as $segment) {
            $result[$segment->getSegmentNumber()] = $segment->getName();
        }
        return $result;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $result = array_map(function ($segment) {
            return $segment->toString();
        }, $this->segments);
        return implode($result, '');
    }

    /**
     * @return string
     */
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

    /**
     * @param array $segments
     * @return array
     */
    protected function wrapWithEnvelope(array $segments)
    {
        return array_merge([$this->envelope[0]], $segments, [$this->envelope[1]]);
    }
}
