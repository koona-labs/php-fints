<?php

namespace  Abiturma\PhpFints\Segments;

/**
 * 2FA/Tan
 *
 * Fields
 * - 2 Tan Process
 * ...
 *
 * @package Abiturma\PhpFints
 */
class HKTAN extends AbstractSegment
{

    const VERSION = 6;

    const NAME = 'HKTAN';

    protected function boot()
    {
        $this->addElement(4)->addElement('HKIDN'); 
    }

}
