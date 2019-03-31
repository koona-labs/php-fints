<?php

namespace Abiturma\PhpFints\Models;


use Abiturma\PhpFints\Misc\HasAttributes;

abstract class AbstractModel
{
    use HasAttributes; 
    
    public function __construct($attributes = [])
    {
        $this->attributes = $attributes; 
    }
    
    
    
}