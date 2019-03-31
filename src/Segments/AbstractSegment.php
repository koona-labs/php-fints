<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\Dialog\DialogParameters;
use Abiturma\PhpFints\Misc\HoldsDataElements;
use Abiturma\PhpFints\Misc\OutputsSegmentAsString;

abstract class AbstractSegment
{

    use HoldsDataElements, OutputsSegmentAsString; 
    
    /*
     * Data-Elements of SegmentHead
     * * Code
     * * SegmentNumber
     * * SegmentVersion
     */
    
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

    public function setSegmentNumber($segmentNumber)
    {
        $this->segmentNumber = $segmentNumber;
        return $this;
    }

    public function getSegmentNumber()
    {
        return $this->segmentNumber; 
    }

    public function incrementSegmentNumber()
    {
        $this->setSegmentNumber($this->segmentNumber+1);
    }

    public function mergeDialogParameters(DialogParameters $parameters)
    {
        return $this; 
    }

    public function getVersion()
    {
        return static::VERSION; 
    }

    public function getName()
    {
        return static::NAME; 
    }
    
    protected function buildSegmentHead()
    {
        return static::NAME . ':' . $this->segmentNumber . ':' .  $this->getVersion() ;
    }

    protected function getPositionOffset()
    {
        return 2; 
    }


}