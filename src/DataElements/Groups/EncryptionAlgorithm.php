<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\Bin;
use Abiturma\PhpFints\DataElements\DataElementGroup;

class EncryptionAlgorithm extends DataElementGroup
{

    const ENCRYPTION_USAGE = 2; //2 = Owner Symmetry (only one value available)

    const OPERATION_MODE = 2; // 2=CBC,16=ISO-9796-1 ,17=ISO-9796-2, 18=RSASSA-PKC, 19 =RSASSA-PSS, 999 = Mutually negotiated 

    const ALGORITHM_CODE = 13; //13 = 2-Key-Triple-DES, 14 = AES-256

    const ALGORITHM_KEY_VALUE = '00000000';

    const ALGORITHM_KEY_DESCRIPTION = 5; // 5 = symmetric key, 6 = symmetric key encrypted by a public key (RAH/RDH) 

    const ALGORITHM_PARAMETER_IV_DESCRIPTION = 1; // 1 = IVC = Initialization Value Clear Text (only one value available) 


    /*
     * DataFields: 
     * 1 EncryptionUsage
     * 2 OperationMode
     * 3 AlgorithmCode
     * 4 AlgorithmKeyValue
     * 5 AlgorithmKeyDescription
     * 6 AlgorithmParameterIVDescription
     */

    protected function boot()
    {
        $this->addElement(static::ENCRYPTION_USAGE)
            ->addElement(static::OPERATION_MODE)
            ->addElement(static::ALGORITHM_CODE)
            ->addElement(new Bin(static::ALGORITHM_KEY_VALUE))
            ->addElement(static::ALGORITHM_KEY_DESCRIPTION)
            ->addElement(static::ALGORITHM_PARAMETER_IV_DESCRIPTION);
    }


}