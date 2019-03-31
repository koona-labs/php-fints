<?php

namespace Abiturma\PhpFints\DataElements;


class DataElement implements HoldsStringableData
{
    protected $value;

    protected $fixedLength = false;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function fixedLength($length)
    {
        $this->fixedLength = $length;
        return $this;
    }

    public function toString()
    {
        $result = $this->castToString();
        if ($this->fixedLength) {
            $result = str_pad($result, $this->fixedLength, "0", STR_PAD_LEFT);
        }
        return (string)$result;
    }

    public function toRawValue()
    {
        return (string) $this->value; 
    }


    public function castToString()
    {
        $value = (string) $this->value;
        return $this->escapeString($value);
    }

    protected function escapeString($string)
    {
        return str_replace(
            ['?', '@', ':', '+', '\''],
            ['??', '?@', '?:', '?+', '?\''],
            $string
        );
    }

    public static function fromResponseString($string, $binaries = [])
    {
        $matches = []; 
        if(preg_match("/@#(\d+)@/",$string,$matches)) {
            $binary = is_array($binaries) ? $binaries[$matches[1]] : $binaries; 
            return new Bin($binary); 
        }
        
        $value = str_replace(
            ['??', '?@', '?:', '?+', '?\''],
            ['?', '@', ':', '+', '\''],
            $string
        );
        
        
        return new static($value); 
    }


    public static function fromArray(array $array)
    {
        return array_map(function ($value) {
            return new static($value);
        },$array);
    }

    public function clone()
    {
        return clone $this; 
    }


}