<?php

namespace Abiturma\PhpFints\Response\Messages;


use Abiturma\PhpFints\Exceptions\NoStatementOfAccountResponse;
use Abiturma\PhpFints\Exceptions\UnexpectedResponseType;
use Abiturma\PhpFints\Parser\Camt;
use Abiturma\PhpFints\Parser\MT940;
use Abiturma\PhpFints\Response\ResponseSegment;

class StatementOfAccount extends AbstractResponseMessage
{

    public function getTransactions()
    {
        if($statement = $this->response->getFirstOfType('HIKAZ')) {
            return $this->getMtStatement($statement); 
        }
        
        if($statement = $this->response->getFirstOfType('HICAZ')) {
            return $this->getCamtStatement($statement); 
        }
        
        if($this->isOk()) {
            return []; 
        }

        throw new NoStatementOfAccountResponse(); 
        
    }

    public function getOriginalType()
    {
        $segments = array_values(array_intersect(['HKKAZ','HKCAZ'],$this->getOriginalOrder())); 
        if(count($segments) < 1) {
            throw new UnexpectedResponseType('No HKKAZ or HKCAZ segment in message'); 
        }
        return $segments[0]; 
    }

    public function isPaginated()
    {
        return $this->response->isPaginated($this->getOriginalType()); 
    }

    public function getPaginationToken()
    {
        return $this->response->getPaginationToken($this->getOriginalType());     
    }


    protected function getMtStatement(ResponseSegment $statement)
    {
        $mt940 = $statement->getElementAtPosition(2)->toRawValue();     
        return (new MT940)->parseFromString($mt940);     
    }

    protected function getCamtStatement(ResponseSegment $statement)
    {
        $camt = $statement->getElementAtPosition(4)->toRawValue();
        $camt = iconv('UTF-8','ISO-8859-1',$camt); 
        return (new Camt)->parseFromString($camt);
    }
    
    
}