<?php

namespace Abiturma\PhpFints\Encryption;


class NullEncrypter extends AbstractEncrypter {


    protected function encryptSegments($segments)
    {
        $result = array_map(function($segment) {
            return $segment->toString();
        }, $segments);
        return implode($result,'');
    }

    protected function setHeadProps($head,$segments)
    {
        $head->setKeyTypeToCypher(); 
        return $head; 
    }

    public function decrypt($string)
    {
        return $string; 
    }
}