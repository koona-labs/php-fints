<?php

namespace Abiturma\PhpFints\Response;


class Feedback
{
    protected $segment;
    
    protected $reference = null; 

    public function __construct(ResponseSegment $segment)
    {
        $this->segment = $segment;
    }

    public function setReference($reference)
    {
        $this->reference = $reference; 
        return $this; 
    }

    public function getCode()
    {
        return $this->segment->getElementAtPosition(2)->getElementAtPosition(1)->toRawValue();
    }

    public function getElementByCode($code)
    {
        foreach($this->segment->getElements() as $element) {
            if($element->getElementAtPosition(1)->toRawValue() == $code) {
                return $element;     
            }
        }
        return null; 
    }

    public function getReference()
    {
        return $this->reference; 
    }

    public function getPaginationToken()
    {
        return $this->segment->getElementAtPosition(2)->getElementAtPosition(4)->toRawValue(); 
    }

    public function getMessages()
    {
        return array_map(function ($element) {
            return $element->getElementAtPosition(3)->toRawValue();
        }, $this->segment->getElements());
    }

    public function getFullMessage()
    {
        return implode(' | ',$this->getMessages()); 
    }

}