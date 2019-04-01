<?php

namespace Abiturma\PhpFints\Models;

/**
 * Interface HasAccountStatement
 * @package Abiturma\PhpFints
 */
interface HasAccountStatement
{
    /**
     * @return Account
     */
    public function toFinTsAccount();
}
