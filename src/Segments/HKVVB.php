<?php

namespace Abiturma\PhpFints\Segments;

use Abiturma\PhpFints\Dialog\DialogParameters;

/**
 * ProcedureInitialization/Verfahrensvorbereitung
 *
 * Fields
 * - 2 BPD-Version (BPD = Bank Parameter Data)
 * - 3 UPD-Version (User Parameter Data)
 * - 4 DialogLanguage
 * - 5 ProductName
 * - 6 ProductVersion
 *
 * @package Abiturma\PhpFints
 */
class HKVVB extends AbstractSegment
{
    const NAME = 'HKVVB';
    
    const VERSION = 3;
    
    const BPD_VERSION = 0;
    
    const UPD_VERSION = 0;
    
    const DIALOG_LANGUAGE = 0; // 0 = Standard/Default, 1 = DE, 2 = EN, 3 = FR
    
    const PRODUCT_NAME = '5802C9F7CC38DD0CA24451B8E';
    
    const PRODUCT_VERSION = '1.0.6';


    protected function boot()
    {
        $this->addElement(static::BPD_VERSION)
            ->addElement(static::UPD_VERSION)
            ->addElement(static::DIALOG_LANGUAGE)
            ->addElement(static::PRODUCT_NAME)
            ->addElement(static::PRODUCT_VERSION);
    }

    /**
     * @param $version
     * @return $this
     */
    public function setBpdVersion($version)
    {
        $this->setElementAtPosition(2, $version);
        return $this;
    }

    /**
     * @param $version
     * @return $this
     */
    public function setUpdVersion($version)
    {
        $this->setElementAtPosition(3, $version);
        return $this;
    }

    /**
     * @param DialogParameters $parameters
     * @return AbstractSegment|HKVVB
     */
    public function mergeDialogParameters(DialogParameters $parameters)
    {
        return $this->setBpdVersion($parameters->bpdVersion)->setUpdVersion($parameters->updVersion);
    }
}
