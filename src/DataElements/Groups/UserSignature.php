<?php

namespace Abiturma\PhpFints\DataElements\Groups;

use Abiturma\PhpFints\DataElements\DataElementGroup;

/**
 * Class UserSignature
 *
 * Fields
 * - 1 PIN
 * - 2 TAN (optional)
 *
 * @package Abiturma\PhpFints
 */
class UserSignature extends DataElementGroup
{
    protected function boot()
    {
        $this->addElement('pin');
    }

    /**
     * @param $pin
     * @return $this
     */
    public function setPin($pin)
    {
        $this->setElementAtPosition(1, $pin);
        return $this;
    }

    /**
     * @param $tan
     * @return $this
     */
    public function setTan($tan)
    {
        $this->setElementAtPosition(2, $tan);
        return $this;
    }
}
