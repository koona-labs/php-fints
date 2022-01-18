<?php

namespace Abiturma\PhpFints\Encryption;

/**
 * Class NullEncrypter
 * @package Abiturma\PhpFints
 */
class NullEncrypter extends AbstractEncrypter
{


    /**
     * @param $segments
     * @return mixed|string
     */
    protected function encryptSegments($segments)
    {
        $result = array_map(function ($segment) {
            return $segment->toString();
        }, $segments);
        return implode('', $result);
    }

    /**
     * @param $head
     * @param $segments
     * @return mixed
     */
    protected function setHeadProps($head, $segments)
    {
        $head->setKeyTypeToCypher();
        return $head;
    }

    /**
     * @param $string
     * @return mixed
     */
    public function decrypt($string)
    {
        return $string;
    }
}
