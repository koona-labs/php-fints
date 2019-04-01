<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;
use DateTime;

/**
 * Class SecurityDateTime
 * 
 * Fields
 * 1 DateTimeType
 * 2 Date
 * 3 Time
 * 
 * @package Abiturma\PhpFints
 */
class SecurityDateTime extends DataElementGroup
{

    const DATETIME_TYPE = 1; //1=STS, 2=CRT


    protected function boot()
    {
        $now = new DateTime(); 
        $this->addElement(self::DATETIME_TYPE)
        ->addElement($now->format('Ymd'))
        ->addElement($now->format('His'));     
    }

    /**
     * @param DateTime $time
     * @return $this
     */
    public function setTime(DateTime $time)
    {
        $this->setElementAtPosition($time->format('Ymd'),2);
        $this->setElementAtPosition($time->format('His'),3); 
        return $this; 
    }
    
    
    
    
}