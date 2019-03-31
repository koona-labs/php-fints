<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;

class Kti extends DataElementGroup
{

    /*
     * DataFields: 
     * 1 iban (O)
     * 2 bic (O)
     * 3 account number (O)
     * 4 sub account number (0)
     * 5 Kik
     */


    protected function boot()
    {
        $this->addElement('')
            ->addElement('')
            ->addElement('')
            ->addElement('')
            ->addElement((new Kik())->setElementAtPosition(1,'')->setElementAtPosition(2,''));
    }

    public function setIban($iban)
    {
        return $this->setElementAtPosition(1, $iban);
    }

    public function setBic($bic)
    {
        return $this->setElementAtPosition(2,$bic); 
    }

    public function setAccountNumber($accountNumber)
    {
        return $this->setElementAtPosition(3,$accountNumber); 
    }

    public function setSubAccountNumber($subAccountNumber)
    {
        return $this->setElementAtPosition(4,$subAccountNumber); 
    }
    
    public function setKik(Kik $kiK)
    {
        return $this->setElementAtPosition(5, $kiK);
    }
    
    

}