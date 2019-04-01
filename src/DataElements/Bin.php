<?php

namespace Abiturma\PhpFints\DataElements;


/**
 * Class Bin
 * @package Abiturma\PhpFints
 */
class Bin extends DataElement
{

    /**
     * @return string
     */
    public function toString()
    {
        $result = (string) $this->value;
        return '@'. mb_strlen($result). '@'. $result; 
    }
    
    
}