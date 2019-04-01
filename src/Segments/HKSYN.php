<?php

namespace Abiturma\PhpFints\Segments;


/**
 * Synchronisation
 * 
 * Fields
 * - 2 SyncMode
 * 
 * @package Abiturma\PhpFints
 */
class HKSYN extends AbstractSegment
{

    const NAME = 'HKSYN';

    const VERSION = 3;

    const SYNC_MODE = 0; // 0 = No Security, 1 = Authentication, 2 = Auth + Signature, 3 = DS-Key, 4 = DS-Key 
    

    protected function boot()
    {
        $this->addElement(static::SYNC_MODE); 
    }

}