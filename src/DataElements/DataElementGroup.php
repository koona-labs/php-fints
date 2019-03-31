<?php

namespace Abiturma\PhpFints\DataElements;


use Abiturma\PhpFints\Misc\HoldsDataElements;

class DataElementGroup implements HoldsStringableData
{

    use HoldsDataElements; 
    
    public function __construct()
    {
        $this->boot();      
    }

    protected function boot()
    {
        
    }
    
   
    public function toString()
    {
        $result = array_map(function($element) {
            return $element->toString();     
        },$this->elements); 
        
        return implode(':',$result); 
        
    }

    public function __toString()
    {
        return $this->toString(); 
    }


    public function clone()
    {
        $clone = new static(); 
        foreach($this->elements as $position => $element) {
            $clone->setElementAtPosition($position+1,$element->clone()); 
        }
        return $clone; 
    }

    protected function buildNestedGroups()
    {
        return $this;
    }

    public static function fromDataElementGroup(DataElementGroup $dataElementGroup)
    {
        $result = (new static())->setElements($dataElementGroup->getElements()); 
        $result->buildNestedGroups(); 
        return $result; 
    }
    
    
    
}