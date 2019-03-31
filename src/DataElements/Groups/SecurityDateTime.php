<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;
use DateTime;

class SecurityDateTime extends DataElementGroup
{

    const DATETIME_TYPE = 1; //1=STS, 2=CRT

    /*
     * DataFields: 
     * 1 DateTimeType
     * 2 Date
     * 3 Time
     */

    protected function boot()
    {
        $now = new DateTime(); 
        $this->addElement(self::DATETIME_TYPE)
        ->addElement($now->format('Ymd'))
        ->addElement($now->format('His'));     
    }

    public function setTime(DateTime $time)
    {
        $this->setElementAtPosition($time->format('Ymd'),2);
        $this->setElementAtPosition($time->format('His'),3); 
        return $this; 
    }
    
    
    
    
}