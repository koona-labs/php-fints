<?php

namespace Abiturma\PhpFints\DataElements\Groups;

use Abiturma\PhpFints\DataElements\DataElementGroup;

/**
 * Class Ktz
 *
 * Fields
 * - 1 IsSepa
 * - 2 IBAN
 * - 3 BIC
 * - 4 Account Number
 * - 5 Sub Account Number (or Currency)
 * - 6 Kik
 *
 * @package Abiturma\PhpFints
 */
class Ktz extends DataElementGroup
{
    const CURRENCY = 'EUR';



    protected function boot()
    {
        $this->addElement('J')
            ->addElement('iban')
            ->addElement('bic')
            ->addElement(0)
            ->addElement(static::CURRENCY)
            ->addElement(new Kik());
    }

    /**
     * @param $iban
     * @return Ktz
     */
    public function setIban($iban)
    {
        return $this->setElementAtPosition(2, $iban);
    }

    /**
     * @param $bic
     * @return Ktz
     */
    public function setBic($bic)
    {
        return $this->setElementAtPosition(3, $bic);
    }

    /**
     * @param $number
     * @return Ktz
     */
    public function setAccountNumber($number)
    {
        return $this->setElementAtPosition(4, $number);
    }

    /**
     * @param $code
     * @return $this
     */
    public function setBankCode($code)
    {
        $this->getElementAtPosition(6)->setBankCode($code);
        return $this;
    }

    /**
     * @return \Abiturma\PhpFints\DataElements\DataElement
     */
    public function getKik()
    {
        return $this->getElementAtPosition(6);
    }

    /**
     * @return mixed
     */
    public function getBankCode()
    {
        return $this->getKik()->getBankCode();
    }

    /**
     * @return $this|DataElementGroup
     */
    protected function buildNestedGroups()
    {
        if ($this->getElementAtPosition(6) instanceof Kik) {
            return $this;
        }
        
        if ($this->getElementAtPosition(6) instanceof DataElementGroup) {
            $kik = Kik::fromDataElementGroup($this->getElementAtPosition(6));
            $this->setElementAtPosition(6, $kik);
            return $this;
        }

        $kik = (new Kik())
            ->setElementAtPosition(1, $this->getElementAtPosition(6))
             ->setElementAtPosition(2, $this->getElementAtPosition(7));
        $this->setElementAtPosition(6, $kik);
        $this->removeElementAtPosition(7);
        
        
        return $this;
    }
}
