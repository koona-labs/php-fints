<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\Dialog\DialogParameters;

class HNHBK extends AbstractSegment
{

    const NAME = "HNHBK";

    const HBCI_VERSION = 300;

    /*
     * MessageHead/Nachrichtenkopf
     * DataFields: 
     * 2 MessageLength (fixed 12)
     * 3 HBCI-Version
     * 4 DialogId
     * 5 MessageNumber
     */
    

    protected function boot()
    {
        $this->addElement(
            (new DataElement(0))->fixedLength(12)
        )
            ->addElement(static::HBCI_VERSION)
            ->addElement(0)
            ->addElement(1); 
    }


    public function setMessageLength($value)
    {
        return $this->setElementAtPosition(2,(new DataElement($value))->fixedLength(12));
    }

    public function setDialogId($value)
    {
        return $this->setElementAtPosition(4,$value);
    }

    public function setMessageNumber($value)
    {
        return $this->setElementAtPosition(5,$value); 
    }

    public function mergeDialogParameters(DialogParameters $dialogParameters)
    {
        return $this->setMessageNumber($dialogParameters->messageNumber)
            ->setDialogId($dialogParameters->dialogId); 
    }

}