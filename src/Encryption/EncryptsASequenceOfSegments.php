<?php

namespace Abiturma\PhpFints\Encryption;


interface EncryptsASequenceOfSegments
{

    public function encrypt(array $segments);

    public function decrypt($string); 
    
}