<?php

namespace Abiturma\PhpFints\Response\Messages;


use Abiturma\PhpFints\Response\HoldsDialogParameters;
use Abiturma\PhpFints\Segments\HKVVB;

/**
 * Class SyncResponse
 * @package Abiturma\PhpFints
 */
class SyncResponse extends AbstractResponseMessage implements HoldsDialogParameters
{
    protected $tanErrorCode = 3920;

    /**
     * @return mixed
     */
    public function getBpd()
    {
        return $this->getFirstOfType('HIBPA');
    }

    /**
     * @return bool|int
     */
    public function getBpdVersion()
    {
        $bpd = $this->getBpd();

        return $bpd ? (int)$bpd->getElementAtPosition(2)->toString() : false;
    }

    /**
     * @return mixed
     */
    public function getUpd()
    {
        return $this->getFirstOfType('HIUPA');
    }

    /**
     * @return bool|int
     */
    public function getUpdVersion()
    {
        $upd = $this->getUpd();
        return $upd ? (int)$upd->getElementAtPosition(3)->toString() : false;
    }

    /**
     * @return string
     */
    public function getSystemId()
    {
        return $this->getFirstOfType('HISYN')->getElementAtPosition(2)->toRawValue();
    }

    /**
     * @return string|null
     */
    public function getCamtVersion()
    {
        if(!$hicazs = $this->getFirstOfType('HICAZS')) {
            return null; 
        }
        
        return $hicazs->getElementAtPosition(5)->getElementAtPosition(4)->toRawValue(); 
    }

    /**
     * @return string|null
     */
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

    /**
     * @return string|null
     */
    public function getSwiftStatementVersion()
    {
        $result = array_map(function($segment) {
            return $segment->getVersion(); 
        }, $this->getByType('HIKAZS')); 
        
        $result = array_intersect([6,7],$result); 
        
        return count($result) ? max($result) : null; 
        
    }


    /**
     * @return array
     */
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