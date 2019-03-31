<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\Credentials\HoldsCredentials;
use Abiturma\PhpFints\DataElements\Groups\Kik;
use Abiturma\PhpFints\Dialog\DialogParameters;

class HKIDN extends AbstractSegment
{

    const NAME = 'HKIDN'; 
    
    const VERSION = 2;

    const SYSTEM_STATUS_ID = 1; // 1 = SystemId is required, 0 = SystemId is not required
    
    /*
    * Identification/Identifikation
    * DataFields: 
    * 2 Kik (Bank-identifier = Kreditinstitutkennung)
    * 3 Username
    * 4 SystemId
    * 5 SystemStatus
    */


    protected function boot()
    {
        $this->addElement(new Kik())
            ->addElement('username')
            ->addElement(0)
            ->addElement(static::SYSTEM_STATUS_ID); 
    }

    public function setBankCode($bankCode)
    {
        $this->getElementAtPosition(2)->setBankCode($bankCode); 
        return $this; 
    }

    public function setUsername($username)
    {
        $this->setElementAtPosition(3,$username); 
        return $this; 
    }

    public function setSystemId($systemId)
    {
        $this->setElementAtPosition(4,$systemId); 
        return $this; 
    }

    public function fromCredentials(HoldsCredentials $credentials)
    {
        $username = $credentials->username(); 
        $bankCode = $credentials->bankCode(); 
        return $this->setUsername($username)->setBankCode($bankCode); 
        
    }

    public function mergeDialogParameters(DialogParameters $parameters)
    {
        return $this->setSystemId($parameters->systemId);  
    }
    
    
    
}