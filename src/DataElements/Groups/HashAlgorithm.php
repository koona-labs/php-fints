<?php

namespace Abiturma\PhpFints\DataElements\Groups;

use Abiturma\PhpFints\DataElements\DataElementGroup;

/**
 * Class HashAlgorithm
 *
 * Fields
 * - 1 HashUsage
 * - 2 HashAlgorithmCode
 * - 3 HashAlgorithmParameterDescription
 *
 * @package Abiturma\PhpFints
 */
class HashAlgorithm extends DataElementGroup
{
    const HASH_USAGE = 1; // 1 = OHA = Owner Hashing (only one value available)

    const ALGORITHM_CODE = 999; // 3 = SHA-256, 4 = SHA-384, 5 = SHA-512, 6 = SHA-256/SHA-256, 999 = Mutually negotiated

    const ALGORITHM_PARAMATER_DESCRIPTION = 1; //1 = ICV = initialization value/clear text (only one value available)


    protected function boot()
    {
        $this->addElement(self::HASH_USAGE)
            ->addElement(self::ALGORITHM_CODE)
            ->addElement(self::ALGORITHM_PARAMATER_DESCRIPTION);
    }
}
