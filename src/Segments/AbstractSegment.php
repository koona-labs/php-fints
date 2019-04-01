<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Misc\HoldsDataElements;
use Abiturma\PhpFints\Misc\OutputsSegmentAsString;

/**
 * Class AbstractSegment
 * 
 * Data elements of segment head (= 1st field)
 * - Code
 * - SegmentNumber
 * - SegmentVersion
 * 
 * @package Abiturma\PhpFints
 */
abstract class AbstractSegment
{

    use HoldsDataElements, OutputsSegmentAsString; 
    
    const NAME = "XXXX";

    const VERSION = 3;

    protected $elements = [];

    protected $segmentNumber = 1;

    public function __construct()
    {
        $this->boot();      
    }

    protected function boot()
    {
        
    }

    /**
     * @param $segmentNumber
     * @return $this
     */
    public function setSegmentNumber($segmentNumber)
    {
        $this->segmentNumber = $segmentNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getSegmentNumber()
    {
        return $this->segmentNumber; 
    }

    public function incrementSegmentNumber()
    {
        $this->setSegmentNumber($this->segmentNumber+1);
    }

    /**
     * @param DialogParameters $parameters
     * @return $this
     */
    public function mergeDialogParameters(DialogParameters $parameters)
    {
        return $this; 
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return static::VERSION; 
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME; 
    }

    /**
     * @return string
     */
    protected function buildSegmentHead()
    {
        return static::NAME . ':' . $this->segmentNumber . ':' .  $this->getVersion() ;
    }

    /**
     * @return int
     */
    protected function getPositionOffset()
    {
        return 2; 
    }


}