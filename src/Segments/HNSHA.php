<?php

namespace Abiturma\PhpFints\Segments;

use Abiturma\PhpFints\DataElements\Groups\UserSignature;

/**
 * EndOfSignature/SignaturAbschluss
 *
 * Fields:
 * - 2 SecurityControlReference
 * - 3 ResultOfValidation (N)
 * - 4 UserSignature
 *
 * @package Abiturma\PhpFints
 */
class HNSHA extends AbstractSegment
{
    const NAME = 'HNSHA';

    const VERSION = 2;


    protected function boot()
    {
        $this->addElement('')
            ->addElement('')
            ->addElement(new UserSignature());
    }

    /**
     * @param HNSHK $HNSHK
     * @return $this
     */
    public function setSecurityControlReference(HNSHK $HNSHK)
    {
        $this->setElementAtPosition(2, $HNSHK->getSecurityControlReference());
        return $this;
    }

    /**
     * @param $pin
     * @return $this
     */
    public function setPin($pin)
    {
        $this->getElementAtPosition(4)->setPin($pin);
        return $this;
    }

    /**
     * @param $tan
     * @return $this
     */
    public function setTan($tan)
    {
        $this->getElementAtPosition(4)->setTan($tan);
        return $this;
    }
}
