<?php

namespace Abiturma\PhpFints\Misc;


use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\DataElements\HoldsStringableData;

trait HoldsDataElements
{


    protected $elements = [];
    

    public function addElement($element)
    {
        $this->elements[] = $this->normalizeDataElement($element);
        return $this;
    }

    public function setElementAtPosition($position, $element)
    {
        $this->elements[$position-$this->getPositionOffset()] = $this->normalizeDataElement($element);
        return $this;
    }

    public function getElementAtPosition($position)
    {
        if(array_key_exists($position-$this->getPositionOffset(),$this->elements)) {
            return $this->elements[$position-$this->getPositionOffset()];
        }
        return new DataElement('');
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function setElements(array $elements)
    {
        $this->elements = $elements;
        return $this;
    }


    protected function removeElementAtPosition($position)
    {
        if(array_key_exists($position-$this->getPositionOffset(),$this->elements)) {
            unset($this->elements[$position-$this->getPositionOffset()]);
        } 
        return $this; 
    }

    protected function normalizeDataElement($element)
    {
        if($element instanceof HoldsStringableData) {
            return $element;
        }

        return new DataElement($element);
    }

    protected function getPositionOffset()
    {
        return 1; 
    }
    
    
    
    
    
    
    
}