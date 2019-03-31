<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;

class Kik extends DataElementGroup
{

    const COUNTRY_CODE = 280;

    /*
     * DataFields: 
     * 1 CountryCode
     * 2 BankCode
     */
    
    
    protected function boot()
    {
        $this->addElement(static::COUNTRY_CODE)->addElement(00000000); 
    }

    public function setBankCode($code)
    {
        $this->setElementAtPosition(2,$code); 
        return $this; 
    }

    public function getBankCode()
    {
        return $this->getElementAtPosition(2); 
    }
    

}