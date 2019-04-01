<?php

namespace Abiturma\PhpFints\Encryption;

/**
 * Interface EncryptsASequenceOfSegments
 * @package Abiturma\PhpFints
 */
interface EncryptsASequenceOfSegments
{

    /**
     * @param array $segments
     * @return mixed
     */
    public function encrypt(array $segments);

    /**
     * @param $string
     * @return mixed
     */
    public function decrypt($string);
}
