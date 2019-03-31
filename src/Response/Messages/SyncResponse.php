<?php

namespace Abiturma\PhpFints\Response\Messages;


use Abiturma\PhpFints\Response\HoldsDialogParameters;
use Abiturma\PhpFints\Response\Response;
use Abiturma\PhpFints\Segments\HKVVB;
use Abiturma\PhpFints\Segments\HNSHK;

class SyncResponse extends AbstractResponseMessage implements HoldsDialogParameters
{
    protected $tanErrorCode = 3920; 

    public function getBpd()
    {
        return $this->getFirstOfType('HIBPA');
    }

    public function getBpdVersion()
    {
        $bpd = $this->getBpd();

        return $bpd ? (int)$bpd->getElementAtPosition(2)->toString() : false;
    }

    public function getUpd()
    {
        return $this->getFirstOfType('HIUPA');
    }

    public function getUpdVersion()
    {
        $upd = $this->getUpd();
        return $upd ? (int)$upd->getElementAtPosition(3)->toString() : false;
    }

    public function getSystemId()
    {
        return $this->getFirstOfType('HISYN')->getElementAtPosition(2)->toRawValue();
    }

    public function getCamtVersion()
    {
        if(!$hicazs = $this->getFirstOfType('HICAZS')) {
            return null; 
        }
        
        return $hicazs->getElementAtPosition(5)->getElementAtPosition(4)->toRawValue(); 
    }

    public function getTanFunctionCode()
    {
        $feedback = array_filter($this->getSegmentalFeedback(),function($feedback) {
            return $feedback->getReference() ==  HKVVB::NAME; 
        }); 
        
        if(!$feedback) {
            return null; 
        }
        
        $feedback = array_shift($feedback);
        
        $errorMessage = $feedback->getElementByCode($this->tanErrorCode); 
        
        if(!$errorMessage) {
            return null; 
        }
        
        return $errorMessage->getElementAtPosition(4)->toRawValue(); 
        
    }

    public function getSwiftStatementVersion()
    {
        $result = array_map(function($segment) {
            return $segment->getVersion(); 
        }, $this->getByType('HIKAZS')); 
        
        $result = array_intersect([6,7],$result); 
        
        return count($result) ? max($result) : null; 
        
    }



    public function toMergableParameters()
    {
        $result = [
            'dialogId' => $this->getDialogId(),
            'systemId' => $this->getSystemId(),
            'updVersion' => $this->getUpdVersion(),
            'bpdVersion' => $this->getBpdVersion(),
            'camtVersion' => $this->getCamtVersion(),
            'swiftStatementVersion' => $this->getSwiftStatementVersion(),
            'tanFunctionCode' => $this->getTanFunctionCode()
        ];
        return array_filter($result,
            function ($entry) {
                return $entry !== false;
            });
    }


   


}