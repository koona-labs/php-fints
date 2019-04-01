<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\DataElements\Groups\EncryptionAlgorithm;
use Abiturma\PhpFints\DataElements\Groups\KeyName;
use Abiturma\PhpFints\DataElements\Groups\SecurityDateTime;
use Abiturma\PhpFints\DataElements\Groups\SecurityIdentificationDetails;
use Abiturma\PhpFints\DataElements\Groups\SecurityProfile;

class HNVSK extends AbstractSegment
{

    const NAME = 'HNVSK'; 
    
    const VERSION = 3;
    
    const COMPRESS_FUNCTION_CODE = 0; // 0 = None, 1 = LZW, 2 = COM, 3 = LZSS, 4 = LZHuf, 5 = ZIP, 6 = GZIP, ...

    const SECURITY_FUNCTION_CODE = 998; //I'm not sure why
    
    /*
     * EncryptionHead/VerschlüsselungsKopf
     * DataFields: 
     * 2 SecurityProfile
     * 3 SecurityFunctionCode
     * 4 SecurityRole
     * 5 SecurityIdentificationDetails
     * 6 SecurityDateTime
     * 7 EncryptionAlgorithm
     * 8 KeyName
     * 9 CompressFunction 
    */
    

    protected function boot()
    {
    
        $this->addElement(new SecurityProfile())
            ->addElement(static::SECURITY_FUNCTION_CODE)
            ->addElement(HNSHK::SECURITY_ROLE)
            ->addElement(new SecurityIdentificationDetails())
            ->addElement(new SecurityDateTime())
            ->addElement(new EncryptionAlgorithm())
            ->addElement(new KeyName())
            ->addElement(static::COMPRESS_FUNCTION_CODE); 
        
    }

    public function fromSignatureHead(HNSHK $signatureHead)
    {
        //FieldNumbers in HNVSK mapped to according FieldNumber in HNSHK 
        $fieldMap = [
            2 => 2,
            4 => 6, 
            5 => 7, 
            6 => 9,
            8 => 12
        ];  
        foreach($fieldMap as $ownPosition => $otherPosition) {
            $this->setElementAtPosition($ownPosition, $signatureHead->getElementAtPosition($otherPosition)->clone()); 
        }
        return $this; 
    }

    public function setKeyTypeToCypher()
    {
        $this->getElementAtPosition(8)->setKeyTypeToCypher();
        return $this; 
    }
    
}