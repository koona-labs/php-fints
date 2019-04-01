<?php

namespace Abiturma\PhpFints\DataElements\Groups;

use Abiturma\PhpFints\DataElements\DataElementGroup;

/**
 * Class Ktv
 *
 * Fields
 * - 1 Account Number
 * - 2 Sub Account Number (or Currency)
 * - 3 Kik
 *
 * @package Abiturma\PhpFints
 */
class Ktv extends DataElementGroup
{
    const CURRENCY = 'EUR';



    protected function boot()
    {
        $this->addElement(0)
            ->addElement(static::CURRENCY)
            ->addElement(new Kik());
    }

    /**
     * @param $code
     * @return $this
     */
    public function setBankCode($code)
    {
        $this->getElementAtPosition(3)->setBankCode($code);
        return $this;
    }

    /**
     * @param $number
     * @return Ktv
     */
    public function setAccountNumber($number)
    {
        return $this->setElementAtPosition(1, $number);
    }
}
