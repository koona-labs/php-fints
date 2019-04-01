<?php

namespace Abiturma\PhpFints\Misc;


/**
 * Trait OutputsSegmentAsString
 * @package Abiturma\PhpFints
 */
trait OutputsSegmentAsString
{
    
    public static $SEPARATOR = "'";

    /**
     * @return string
     */
    public function toString()
    {
        $result = array_map(function ($de) {
            return $de->toString();
        }, $this->getElements());
        array_unshift($result,$this->buildSegmentHead());

        return implode('+',$result). static::$SEPARATOR;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    protected function buildSegmentHead()
    {
        return 'XXXX:0:0:0'; 
    }
}