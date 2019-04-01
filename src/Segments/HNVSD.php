<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\DataElements\Bin;

/**
 * EncryptedData/VerschlüsselteDaten
 * 
 * Fields
 * - 2 EncryptedData (bin)
 * 
 * @package Abiturma\PhpFints
 */
class HNVSD extends AbstractSegment
{
    
    const NAME = 'HNVSD'; 
    
    const VERSION = 1;


    /**
     * @param $data
     * @return $this
     */
    public function setEncryptedData($data)
    {
        $this->setElementAtPosition(1,new Bin($data));
        return $this; 
    }
    
    
}

