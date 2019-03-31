<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\DataElements\Bin;

class HNVSD extends AbstractSegment
{
    
    const NAME = 'HNVSD'; 
    
    const VERSION = 1;


    /*
     * EncryptedData/VerschlüsselteDaten
     * DataFields: 
     * 2 EncryptedData (bin)
     */
    

    public function setEncryptedData($data)
    {
        $this->setElementAtPosition(1,new Bin($data));
        return $this; 
    }
    
    
}

