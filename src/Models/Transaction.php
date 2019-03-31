<?php

namespace Abiturma\PhpFints\Models;


class Transaction extends AbstractModel
{
    protected $appends = ['amount']; 
    
    public function getAmountAttribute()
    {
        return number_format($this->base_amount/100,2); 
    }
    
}