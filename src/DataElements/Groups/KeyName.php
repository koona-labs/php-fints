<?php

namespace Abiturma\PhpFints\DataElements\Groups;

use Abiturma\PhpFints\DataElements\DataElementGroup;

/**
 * Class KeyName
 *
 * Fields
 * - 1 Kik (Bank-identifier = Kreditinstitutkennung)
 * - 2 Username
 * - 3 KeyType
 * - 4 KeyNumber
 * - 5 KeyVersion
 *
 * @package Abiturma\PhpFints\Groups
 */
class KeyName extends DataElementGroup
{
    const KEY_TYPE = 'S';  // D = digital signature, S = signing key, v = cypher key

    
    protected function boot()
    {
        $this->addElement(new Kik())
            ->addElement('Username')
            ->addElement(static::KEY_TYPE)
            ->addElement(0)
            ->addElement(0)
        ;
    }

    /**
     * @return $this
     */
    public function setKeyTypeToCypher()
    {
        $this->setElementAtPosition(3, 'V');
        return $this;
    }
}
