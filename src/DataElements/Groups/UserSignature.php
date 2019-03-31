<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;

class UserSignature extends DataElementGroup
{

    /*
     * DataFields: 
     * 1 PIN
     * 2 TAN (optional)
     */
    
    protected function boot()
    {
        $this->addElement('pin'); 
    }

    public function setPin($pin)
    {
        $this->setElementAtPosition(1,$pin); 
        return $this; 
    }

    public function setTan($tan)
    {   
        $this->setElementAtPosition(2,$tan); 
        return $this; 
    }
    
    
    
}