<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;

/**
 * Class Kti
 * 
 * Fields
 * - 1 iban (O)
 * - 2 bic (O)
 * - 3 account number (O)
 * - 4 sub account number (0)
 * - 5 Kik
 * 
 * @package Abiturma\PhpFints
 */
class Kti extends DataElementGroup
{

    protected function boot()
    {
        $this->addElement('')
            ->addElement('')
            ->addElement('')
            ->addElement('')
            ->addElement((new Kik())->setElementAtPosition(1,'')->setElementAtPosition(2,''));
    }

    /**
     * @param $iban
     * @return Kti
     */
    public function setIban($iban)
    {
        return $this->setElementAtPosition(1, $iban);
    }

    /**
     * @param $bic
     * @return Kti
     */
    public function setBic($bic)
    {
        return $this->setElementAtPosition(2,$bic); 
    }

    /**
     * @param $accountNumber
     * @return Kti
     */
    public function setAccountNumber($accountNumber)
    {
        return $this->setElementAtPosition(3,$accountNumber); 
    }

    /**
     * @param $subAccountNumber
     * @return Kti
     */
    public function setSubAccountNumber($subAccountNumber)
    {
        return $this->setElementAtPosition(4,$subAccountNumber); 
    }

    /**
     * @param Kik $kiK
     * @return Kti
     */
    public function setKik(Kik $kiK)
    {
        return $this->setElementAtPosition(5, $kiK);
    }
    
    

}