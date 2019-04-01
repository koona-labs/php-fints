<?php

namespace Abiturma\PhpFints\DataElements;

/**
 * Interface HoldsStringableData
 * @package Abiturma\PhpFints
 */
interface HoldsStringableData
{

    /**
     * @return string
     */
    public function toString();

    /*
     * @return HoldsStringableData
     */
    public function clone();
}
