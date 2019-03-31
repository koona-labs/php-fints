<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\Dialog\DialogParameters;

class HNHBS extends AbstractSegment
{
    
    const NAME = 'HNHBS'; 
    
    const VERSION = 1;
    
    /*
     * EndOfMessage/Nachrichtenabschluss
     * DataFields: 
     * * MessageNumber
    */

    public function boot()
    {
        $this->elements = [
            new DataElement(1),
        ];
    }

    public function setMessageNumber($value)
    {
        $this->elements[0] = (new DataElement($value));
        return $this;
    }

    public function setElements(array $dataElements = [])
    {
        return $this;
    }

    public function mergeDialogParameters(DialogParameters $dialogParameters)
    {
        return $this->setMessageNumber($dialogParameters->messageNumber); 
    }
    
    

    
}