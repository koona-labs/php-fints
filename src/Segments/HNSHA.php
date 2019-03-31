<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\DataElements\Groups\UserSignature;

class HNSHA extends AbstractSegment
{
    const NAME = 'HNSHA';

    const VERSION = 2;

    /*
     * EndOfSignature/SignaturAbschluss
     * DataFields: 
     * 2 SecurityControlReference
     * 3 ResultOfValidation (N)
     * 4 UserSignature
     */


    protected function boot()
    {
        $this->addElement('')
            ->addElement('')
            ->addElement(new UserSignature());
    }

    public function setSecurityControlReference(HNSHK $HNSHK)
    {
        $this->setElementAtPosition(2, $HNSHK->getSecurityControlReference());
        return $this;
    }

    public function setPin($pin)
    {
        $this->getElementAtPosition(4)->setPin($pin); 
        return $this; 
    }

    public function setTan($tan)
    {
        $this->getElementAtPosition(4)->setTan($tan);
        return $this; 
    }


}