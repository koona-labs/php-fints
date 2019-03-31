<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;

class SignatureAlgorithm extends DataElementGroup
{

    const SIGNATURE_USAGE = 6; // 6 = OSG = Owner Signing (only one value available)

    const ALGORITHM_CODE = 10; // 1 = not allowed, 10 = RSA

    const OPERATION_MODE = 16; // 2=CBC,16=ISO-9796-1 ,17=ISO-9796-2, 18=RSASSA-PKC, 19 =RSASSA-PSS, 999 = Mutually negotiated 


    /*
     * DataFields: 
     * 1 SignatureUsage
     * 2 AlgorithmCode
     * 3 OperationMode
     */
    
    
    protected function boot()
    {
        $this->addElement(self::SIGNATURE_USAGE)
            ->addElement(self::ALGORITHM_CODE)
            ->addElement(self::OPERATION_MODE);
    }
    
    
}