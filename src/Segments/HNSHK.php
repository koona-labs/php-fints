<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\DataElements\Groups\HashAlgorithm;
use Abiturma\PhpFints\DataElements\Groups\KeyName;
use Abiturma\PhpFints\DataElements\Groups\SecurityDateTime;
use Abiturma\PhpFints\DataElements\Groups\SecurityIdentificationDetails;
use Abiturma\PhpFints\DataElements\Groups\SecurityProfile;
use Abiturma\PhpFints\DataElements\Groups\SignatureAlgorithm;
use Abiturma\PhpFints\Dialog\DialogParameters;
use DateTime;

class HNSHK extends AbstractSegment
{

    const NAME = 'HNSHK';

    const VERSION = 4;

    const SECURITY_FUNCTION_CODE = 999; // legacy not parsed for FinTs-Version >= 300

    const SECURITY_SCOPE = 1; //1 = SHM, 2 = SHT 

    const SECURITY_ROLE = 1;  // 1 = ISS = Signer is also author, 2 = CON = Signer is supporter, 3 = WIT = Signer is witness 


    /*
     * SignatureHead/SignaturKopf
     * DataFields: 
     * 2 SecurityProfile
     * 3 SecurityFunctionCode
     * 4 SecurityControlReference 
     * 5 SecurityScope
     * 6 SecurityRole
     * 7 SecurityIdentificationDetails
     * 8 SecurityReferenceNumber
     * 9 SecurityDateTime
     * 10 HashAlgorithm
     * 11 SignatureAlgorithm
     * 12 KeyName
     */


    protected function boot()
    {
        $this->addElement(new SecurityProfile())
            ->addElement(static::SECURITY_FUNCTION_CODE)
            ->addElement(rand(10000000000000, 99999999999999))
            ->addElement(static::SECURITY_SCOPE)
            ->addElement(static::SECURITY_ROLE)
            ->addElement(new SecurityIdentificationDetails())
            ->addElement($this->generateSecurityReferenceNumber())
            ->addElement(new SecurityDateTime())
            ->addElement(new HashAlgorithm())
            ->addElement(new SignatureAlgorithm())
            ->addElement(new KeyName());

    }

    public function setBankCode($bankCode)
    {
        $this->getElementAtPosition(12)->getElementAtPosition(1)->setBankCode($bankCode);
        return $this;
    }

    public function setUsername($username)
    {
        $this->getElementAtPosition(12)->setElementAtPosition(2, $username);
        return $this;
    }

    public function getSecurityControlReference()
    {
        return $this->getElementAtPosition(4)->toString(); 
    }

    public function setTanFunctionCode($code)
    {
        return $this->setElementAtPosition(3,$code); 
    }

    protected function generateSecurityReferenceNumber()
    {
        return 1; 
    }

    public function setSystemId($id)
    {
        $this->getElementAtPosition(7)->setSystemId($id); 
        return $this; 
    }

    public function mergeDialogParameters(DialogParameters $parameters)
    {
        if($parameters->systemId) {
            $this->setSystemId($parameters->systemId);
        }
        if($parameters->tanFunctionCode) {
            $this->setTanFunctionCode($parameters->tanFunctionCode); 
        }
        return $this; 
    }

}