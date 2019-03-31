<?php

namespace Abiturma\PhpFints\Misc;


trait OutputsSegmentAsString
{
    
    public static $SEPARATOR = "'"; 
    
    public function toString()
    {
        $result = array_map(function ($de) {
            return $de->toString();
        }, $this->getElements());
        array_unshift($result,$this->buildSegmentHead());

        return implode('+',$result). static::$SEPARATOR;
    }

    public function __toString()
    {
        return $this->toString();
    }

    protected function buildSegmentHead()
    {
        return 'XXXX:0:0:0'; 
    }
}