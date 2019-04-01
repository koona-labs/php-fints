<?php

namespace Abiturma\PhpFints\DataElements\Groups;

use Abiturma\PhpFints\DataElements\DataElementGroup;

/**
 * Class Kik
 *
 * Fields
 * - 1 CountryCode
 * - 2 BankCode
 *
 * @package Abiturma\PhpFints
 */
class Kik extends DataElementGroup
{
    const COUNTRY_CODE = 280;
    
    
    protected function boot()
    {
        $this->addElement(static::COUNTRY_CODE)->addElement(00000000);
    }

    /**
     * @param $code
     * @return $this
     */
    public function setBankCode($code)
    {
        $this->setElementAtPosition(2, $code);
        return $this;
    }

    /**
     * @return \Abiturma\PhpFints\DataElements\DataElement
     */
    public function getBankCode()
    {
        return $this->getElementAtPosition(2);
    }
}
