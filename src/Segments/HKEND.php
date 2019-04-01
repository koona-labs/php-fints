<?php

namespace Abiturma\PhpFints\Segments;



use Abiturma\PhpFints\Dialog\DialogParameters;

/**
 * EndOfDialog/DialogAbschluss
 * 
 * Fields
 * - 2 DialogId
 * 
 * @package Abiturma\PhpFints
 */
class HKEND extends AbstractSegment
{

    const NAME = 'HKEND'; 
    
    const VERSION = 1;

    /*
    
     */

    protected function boot()
    {
        $this->addElement(0);
    }

    /**
     * @param $id
     * @return $this
     */
    public function setDialogId($id)
    {
        $this->setElementAtPosition(2,$id); 
        return $this; 
    }

    /**
     * @param DialogParameters $dialogParameters
     * @return AbstractSegment|HKEND
     */
    public function mergeDialogParameters(DialogParameters $dialogParameters)
    {
        return $this->setDialogId($dialogParameters->dialogId); 
    }
    
}