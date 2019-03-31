<?php

namespace Abiturma\PhpFints\Segments;


class HKSYN extends AbstractSegment
{

    const NAME = 'HKSYN';

    const VERSION = 3;

    const SYNC_MODE = 0; // 0 = No Security, 1 = Authentication, 2 = Auth + Signature, 3 = DS-Key, 4 = DS-Key 
    
    /*
     * Synchronisation
     * DataFields: 
     * 2 SyncMode 
     */

    protected function boot()
    {
        $this->addElement(static::SYNC_MODE); 
    }

}