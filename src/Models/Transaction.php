<?php

namespace Abiturma\PhpFints\Models;


/**
 * Class Transaction
 * @package Abiturma\PhpFints
 */
class Transaction extends AbstractModel
{
    protected $appends = ['amount'];

    /**
     * @return string
     */
    public function getAmountAttribute()
    {
        return number_format($this->base_amount/100,2); 
    }
    
}