<?php

namespace Abiturma\PhpFints\DataElements;


use Abiturma\PhpFints\Misc\HoldsDataElements;

/**
 * Class DataElementGroup
 * @package Abiturma\PhpFints
 */
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


    /**
     * @return string
     */
    public function toString()
    {
        $result = array_map(function($element) {
            return $element->toString();     
        },$this->elements); 
        
        return implode(':',$result); 
        
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString(); 
    }


    /**
     * Clones this instance recursively
     *
     * @return DataElementGroup
     */
    public function clone()
    {
        $clone = new static(); 
        foreach($this->elements as $position => $element) {
            $clone->setElementAtPosition($position+1,$element->clone()); 
        }
        return $clone; 
    }

    /**
     * @return $this
     */
    protected function buildNestedGroups()
    {
        return $this;
    }

    /**
     * @param DataElementGroup $dataElementGroup
     * @return DataElementGroup
     */
    public static function fromDataElementGroup(DataElementGroup $dataElementGroup)
    {
        $result = (new static())->setElements($dataElementGroup->getElements()); 
        $result->buildNestedGroups(); 
        return $result; 
    }
    
    
    
}