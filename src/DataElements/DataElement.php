<?php

namespace Abiturma\PhpFints\DataElements;

/**
 * Class DataElement
 * @package Abiturma\PhpFints
 */
class DataElement implements HoldsStringableData
{
    protected $value;

    /**
     * @var bool
     */
    protected $fixedLength = false;

    /**
     * DataElement constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param $length
     * @return $this
     */
    public function fixedLength($length)
    {
        $this->fixedLength = $length;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $result = $this->castToString();
        if ($this->fixedLength) {
            $result = str_pad($result, $this->fixedLength, "0", STR_PAD_LEFT);
        }
        return (string)$result;
    }

    /**
     * @return string
     */
    public function toRawValue()
    {
        return (string) $this->value;
    }


    /**
     * @return string
     */
    public function castToString()
    {
        $value = (string) $this->value;
        return $this->escapeString($value);
    }

    /**
     * @param $string
     * @return string
     */
    protected function escapeString($string)
    {
        return str_replace(
            ['?', '@', ':', '+', '\''],
            ['??', '?@', '?:', '?+', '?\''],
            $string
        );
    }

    /**
     * @param $string
     * @param array $binaries
     * @return Bin|DataElement
     */
    public static function fromResponseString($string, $binaries = [])
    {
        $matches = [];
        if (preg_match("/@#(\d+)@/", $string, $matches)) {
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


    /**
     * @param array $array
     * @return array
     */
    public static function fromArray(array $array)
    {
        return array_map(function ($value) {
            return new static($value);
        }, $array);
    }

    /**
     * Clones this instance recursively
     *
     * @return DataElement
     */
    public function clone()
    {
        return clone $this;
    }
}
