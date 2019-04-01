<?php

namespace Abiturma\PhpFints\Response;

use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\DataElements\DataElementGroup;
use Abiturma\PhpFints\Exceptions\ResponseSyntaxException;
use Abiturma\PhpFints\Misc\HoldsDataElements;
use Abiturma\PhpFints\Misc\OutputsSegmentAsString;

/**
 * Class ResponseSegment
 * @package Abiturma\PhpFints
 */
class ResponseSegment
{
    use HoldsDataElements, OutputsSegmentAsString;

    protected $type;

    protected $segmentNumber;

    protected $version;
    
    protected $relationNumber;

    /**
     * @param $string
     * @param array $binaries
     * @return ResponseSegment
     * @throws ResponseSyntaxException
     */
    public static function parseFromString($string, $binaries = [])
    {
        $elements = preg_split("/(?<!\?)\+|[^\?](\?\?)+\K\+/", $string);
        $head = array_shift($elements);
        $result = static::fromHead($head);
        
        
        foreach ($elements as $element) {
            $subElements = preg_split("/(?<!\?):|[^\?](\?\?)+\K:/", $element);
            if (count($subElements) == 1) {
                $result->addElement(DataElement::fromResponseString($subElements[0], $binaries));
                continue;
            }
            $result->addElement((new DataElementGroup())
                ->setElements(array_map(function ($subElement) use ($binaries) {
                    return DataElement::fromResponseString($subElement, $binaries);
                }, $subElements)));
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return mb_strtoupper($this->type);
    }

    public function getSegmentNumber()
    {
        return $this->segmentNumber;
    }

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return int
     */
    public function getRelationNumber()
    {
        return (int) $this->relationNumber;
    }


    /**
     * @param $head
     * @return ResponseSegment
     * @throws ResponseSyntaxException
     */
    protected static function fromHead($head)
    {
        $headElements = explode(':', $head);
        if (!in_array(count($headElements), [3,4])) {
            throw new ResponseSyntaxException("A Segment's head has to have 3 or 4 Elements: " . implode('|', $headElements));
        }
        $result = new static();
        $result->type = $headElements[0];
        $result->segmentNumber = $headElements[1];
        $result->version = $headElements[2];
        $result->relationNumber = array_key_exists(3, $headElements) ? $headElements[3] : 0;
        return $result;
    }


    /**
     * @return string
     */
    protected function buildSegmentHead()
    {
        return implode(':', [$this->type,$this->segmentNumber,$this->version,$this->relationNumber]);
    }

    /**
     * @return int
     */
    protected function getPositionOffset()
    {
        return 2;
    }
}
