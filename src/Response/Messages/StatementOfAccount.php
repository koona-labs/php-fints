<?php

namespace Abiturma\PhpFints\Response\Messages;


use Abiturma\PhpFints\Exceptions\NoStatementOfAccountResponse;
use Abiturma\PhpFints\Exceptions\UnexpectedResponseType;
use Abiturma\PhpFints\Parser\Camt;
use Abiturma\PhpFints\Parser\MT940;
use Abiturma\PhpFints\Response\ResponseSegment;

/**
 * Class StatementOfAccount
 * @package Abiturma\PhpFints
 */
class StatementOfAccount extends AbstractResponseMessage
{

    /**
     * @return array
     * @throws NoStatementOfAccountResponse
     * @throws \Genkgo\Camt\Exception\ReaderException
     */
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

    /**
     * @return string
     * @throws UnexpectedResponseType
     */
    public function getOriginalType()
    {
        $segments = array_values(array_intersect(['HKKAZ','HKCAZ'],$this->getOriginalOrder())); 
        if(count($segments) < 1) {
            throw new UnexpectedResponseType('No HKKAZ or HKCAZ segment in message'); 
        }
        return $segments[0]; 
    }

    /**
     * @return bool
     * @throws UnexpectedResponseType
     */
    public function isPaginated()
    {
        return $this->response->isPaginated($this->getOriginalType()); 
    }

    /**
     * @return string|null
     * @throws UnexpectedResponseType
     */
    public function getPaginationToken()
    {
        return $this->response->getPaginationToken($this->getOriginalType());     
    }


    /**
     * @param ResponseSegment $statement
     * @return array
     */
    protected function getMtStatement(ResponseSegment $statement)
    {
        $mt940 = $statement->getElementAtPosition(2)->toRawValue();     
        return (new MT940)->parseFromString($mt940);     
    }

    /**
     * @param ResponseSegment $statement
     * @return array
     * @throws \Genkgo\Camt\Exception\ReaderException
     */
    protected function getCamtStatement(ResponseSegment $statement)
    {
        $camt = $statement->getElementAtPosition(4)->toRawValue();
        $camt = iconv('UTF-8','ISO-8859-1',$camt); 
        return (new Camt)->parseFromString($camt);
    }
    
    
}