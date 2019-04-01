<?php

namespace Abiturma\PhpFints\Segments;

use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\Dialog\DialogParameters;

/**
 * EndOfMessage/Nachrichtenabschluss
 *
 * Fields
 * - 2 MessageNumber
 *
 * @package Abiturma\PhpFints
 */
class HNHBS extends AbstractSegment
{
    const NAME = 'HNHBS';
    
    const VERSION = 1;
    

    public function boot()
    {
        $this->elements = [
            new DataElement(1),
        ];
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMessageNumber($value)
    {
        $this->elements[0] = (new DataElement($value));
        return $this;
    }

    /**
     * @param array $dataElements
     * @return $this|AbstractSegment
     */
    public function setElements(array $dataElements = [])
    {
        return $this;
    }

    /**
     * @param DialogParameters $dialogParameters
     * @return AbstractSegment|HNHBS
     */
    public function mergeDialogParameters(DialogParameters $dialogParameters)
    {
        return $this->setMessageNumber($dialogParameters->messageNumber);
    }
}
