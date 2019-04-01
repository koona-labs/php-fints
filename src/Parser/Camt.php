<?php

namespace Abiturma\PhpFints\Parser;


use Abiturma\PhpFints\Models\Transaction;
use BadMethodCallException;
use DateTime;
use Genkgo\Camt\Camt052\MessageFormat\V02;
use Genkgo\Camt\Config;
use Genkgo\Camt\Reader;

/**
 * Class Camt
 * @package Abiturma\PhpFints
 */
class Camt
{

    protected $rawString;

    /**
     * @param $rawString
     * @return array
     * @throws \Genkgo\Camt\Exception\ReaderException
     */
    public function parseFromString($rawString)
    {
        $this->rawString = $rawString;
        return $this->prepare()->parse();
    }

    /**
     * @return array
     * @throws \Genkgo\Camt\Exception\ReaderException
     */
    protected function parse()
    {
        $config = Config::getDefault();
        $config->addMessageFormat(new V02());
        $reader = new Reader($config);
        $entries = $reader->readString($this->rawString)->getRecords()[0]->getEntries();
        return array_map(function ($entry) {
            return $this->entryToTransaction($entry);
        }, $entries);
    }

    /**
     * @return $this
     */
    protected function prepare()
    {
        $this->rawString = preg_replace('/<\/Ustrd>\s*<Ustrd>/mi','',$this->rawString); 
        return $this; 
    }

    /**
     * @param $entry
     * @return Transaction
     * @throws \Exception
     */
    protected function entryToTransaction($entry)
    {
        $bookingDate = new DateTime($entry->getBookingDate()->format('Y-m-d')); 
        $valueDate = new DateTime($entry->getValueDate()->format('Y-m-d'));
        $amount = $entry->getAmount()->getAmount();
        $currency = $entry->getAmount()->getCurrency()->getCode(); 
        $codes = explode('+',$entry->getBankTransactionCode()->getProprietary()->getCode());
        
        
        $transactionCode = $codes[1];
        $primaNota = $codes[2]; 
        
        $details = $entry->getTransactionDetail();
        
        $index = $amount>0 ? 1 : 0; 
        
        $remote_account_number = ''; 
        $remote_bank_code = ''; 
        $remote_name = ''; 
        
        if(array_key_exists($index,$details->getRelatedParties())) {
            $relatedParty = $details->getRelatedParties()[$index];
            $remote_account_number = $relatedParty->getAccount()->getIban()->getIban(); 
            $remote_name = $relatedParty->getRelatedPartyType()->getName(); 
        }
        
        if(array_key_exists($index,$details->getRelatedAgents())) {
            $remote_bank_code = $details->getRelatedAgents()[$index]->getRelatedAgentType()->getBic();
        }
        
        try {
            $description = $details->getRemittanceInformation()->getMessage();  
        }
        catch ( BadMethodCallException $e) {
            $description = ''; 
        }
        
        try {
            $end_to_end_reference = $details->getReference()->getEndToEndId(); 
        }
        catch( BadMethodCallException $e) {
            $end_to_end_reference = null; 
        }

                
        
        $data = [
            'booking_date' => $bookingDate,
            'value_date' => $valueDate,
            'currency' => $currency, 
            'base_amount' => $amount,
            'remote_name' => $remote_name,
            'remote_account_number' => $remote_account_number,
            'remote_bank_code' => $remote_bank_code,
            'end_to_end_reference' => $end_to_end_reference,
            'description' => $description,
            'transaction_code' => $transactionCode,
            'prima_nota' => $primaNota
        ];

        return new Transaction($data); 

    }


}