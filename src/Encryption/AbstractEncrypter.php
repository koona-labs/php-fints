<?php

namespace Abiturma\PhpFints\Encryption;

use Abiturma\PhpFints\Segments\HNSHK;
use Abiturma\PhpFints\Segments\HNVSD;
use Abiturma\PhpFints\Segments\HNVSK;

/**
 * Class AbstractEncrypter
 * @package Abiturma\PhpFints
 */
abstract class AbstractEncrypter implements EncryptsASequenceOfSegments
{
    const ENCRYPTION_HEAD_SEGMENT_NUMBER = 998;

    const ENCRYPTED_DATA_SEGMENT_NUMBER = 999;


    /**
     * @param array $segments
     * @return array
     */
    public function encrypt(array $segments)
    {
        $head = $this->createHead($segments);
        $data = $this->createEncryptedData($segments);
        $head = $this->setHeadProps($head, $segments);
        return [$head, $data];
    }


    //hook to change data fields of head

    /**
     * @param $head
     * @param $segments
     * @return mixed
     */
    protected function setHeadProps($head, $segments)
    {
        return $head;
    }

    /**
     * @param $segments
     * @return HNVSK
     */
    protected function createHead($segments)
    {
        $head = (new HNVSK())->setSegmentNumber(static::ENCRYPTION_HEAD_SEGMENT_NUMBER);
        return $this->isSigned($segments) ? $head->fromSignatureHead($segments[0]) : $head;
    }

    /**
     * @param $segments
     * @return HNVSD
     */
    protected function createEncryptedData($segments)
    {
        return (new HNVSD())
            ->setEncryptedData($this->encryptSegments($segments))
            ->setSegmentNumber(static::ENCRYPTED_DATA_SEGMENT_NUMBER);
    }

    /**
     * @param $segments
     * @return bool
     */
    protected function isSigned($segments)
    {
        return count($segments) > 0 && $segments[0] instanceof HNSHK;
    }

    /**
     * @param $segments
     * @return mixed
     */
    abstract protected function encryptSegments($segments);
}
