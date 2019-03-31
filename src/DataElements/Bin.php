<?php

namespace Abiturma\PhpFints\DataElements;


class Bin extends DataElement
{

    public function toString()
    {
        $result = (string) $this->value;
        return '@'. mb_strlen($result). '@'. $result; 
    }
    
    
}