<?php

namespace Abiturma\PhpFints\Segments;



use Abiturma\PhpFints\Dialog\DialogParameters;

class HKEND extends AbstractSegment
{

    const NAME = 'HKEND'; 
    
    const VERSION = 1;

    /*
     * EndOfDialog/DialogAbschluss
     * DataFields: 
     * * DialogId
     */

    protected function boot()
    {
        $this->addElement(0);
    }

    public function setDialogId($id)
    {
        $this->setElementAtPosition(2,$id); 
        return $this; 
    }

    public function mergeDialogParameters(DialogParameters $dialogParameters)
    {
        return $this->setDialogId($dialogParameters->dialogId); 
    }
    
}