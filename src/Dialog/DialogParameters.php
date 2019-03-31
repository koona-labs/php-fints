<?php

namespace Abiturma\PhpFints\Dialog;


use Abiturma\PhpFints\Misc\HasAttributes;
use Abiturma\PhpFints\Response\HoldsDialogParameters;

class DialogParameters
{
    
    use HasAttributes; 


    public function __construct()
    {
        $this->reset();      
    }

    public function mergeResponse(HoldsDialogParameters $response, $except = [])
    {
        $parameters = $response->toMergableParameters();
        $parameters = array_diff_key($parameters,array_flip($except)); 
        $this->attributes = array_intersect_key($parameters, $this->attributes) + $this->attributes; 
        return $this;
    }

    public function mergeResponseOnlyWith(HoldsDialogParameters $response, $only = [])
    {
        $parameters = $response->toMergableParameters();
        $parameters = array_intersect_key($parameters,array_flip($only));
        $this->attributes = array_intersect_key($parameters, $this->attributes) + $this->attributes;
        return $this;
    }

    public function incrementMessageNumber()
    {
        return $this->setMessageNumber($this->messageNumber + 1); 
    }

    public function reset()
    {
        $this->attributes = [
            'dialogId' => 0,
            'systemId' => 0,
            'updVersion' => 0,
            'bpdVersion' => 0,
            'messageNumber' => 1,
            'camtVersion' => null, 
            'swiftStatementVersion' => null, 
            'paginationToken' => null,
            'tanFunctionCode' => null,
        ];
        return $this;
    }

    

    public function __call($name, $arguments)
    {
        $property = lcfirst(substr($name, 3));
        $set = substr($name, 0, 3);
        if ($set === 'set' && array_key_exists($property,$this->attributes)) {
            $this->attributes[$property] = $arguments[0]; 
        }
        return $this; 
    }


}