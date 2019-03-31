<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;

class Ktv extends DataElementGroup
{

    const CURRENCY = 'EUR'; 

    /*
     * DataFields: 
     * 1 Account Number
     * 2 Sub Account Number (or Currency)
     * 3 Kik
     */


    protected function boot()
    {
        $this->addElement(0)
            ->addElement(static::CURRENCY)
            ->addElement(new Kik()); 
    }

    public function setBankCode($code)
    {
        $this->getElementAtPosition(3)->setBankCode($code); 
        return $this;
    }

    public function setAccountNumber($number)
    {
        return $this->setElementAtPosition(1,$number); 
    }
    
    
    
}