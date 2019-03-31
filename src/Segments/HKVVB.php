<?php

namespace Abiturma\PhpFints\Segments;


use Abiturma\PhpFints\Dialog\DialogParameters;

class HKVVB extends AbstractSegment
{

    const NAME = 'HKVVB'; 
    
    const VERSION = 3;
    
    const BPD_VERSION = 0; 
    
    const UPD_VERSION = 0; 
    
    const DIALOG_LANGUAGE = 0; // 0 = Standard/Default, 1 = DE, 2 = EN, 3 = FR
    
    const PRODUCT_NAME = 'laravel-hbci'; 
    
    const PRODUCT_VERSION = '0.8'; 

    /*
     * ProcedureInitialization/Verfahrensvorbereitung
     * DataFields: 
     * 2 BPD-Version (BPD = Bank Parameter Data) 
     * 3 UPD-Version (User Parameter Data)
     * 4 DialogLanguage 
     * 5 ProductName
     * 6 ProductVersion
     */

    protected function boot()
    {
        $this->addElement(static::BPD_VERSION)
            ->addElement(static::UPD_VERSION)
            ->addElement(static::DIALOG_LANGUAGE)
            ->addElement(static::PRODUCT_NAME)
            ->addElement(static::PRODUCT_VERSION); 
    }

    public function setBpdVersion($version)
    {
        $this->setElementAtPosition(2,$version); 
        return $this; 
    }

    public function setUpdVersion($version)
    {
        $this->setElementAtPosition(3,$version); 
        return $this; 
    }
    
    public function mergeDialogParameters(DialogParameters $parameters)
    {
        return $this->setBpdVersion($parameters->bpdVersion)->setUpdVersion($parameters->updVersion); 
    }
    
    
}