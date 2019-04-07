<?php

namespace Abiturma\PhpFints\Parser;

use Abiturma\PhpFints\Models\Transaction;
use DateTime;

/**
 * Class MT940
 * @package Abiturma\PhpFints
 */
class MT940
{
    protected $descriptionMap = [
        'SVWZ' => 'description',
        'EREF' => 'end_to_end_reference',
        'KREF' => 'kref',
        'MREF' => 'mref',
        'CREF' => 'cref',
    ];
    
    protected $unwantedFields = [
        'kref',
        'mref',
        'cref'
    ]; 

    protected $fieldMap = [
        'prima_nota' => 10,
        'remote_bank_code' => 30,
        'remote_account_number' => 31,
        'remote_name' => [32, 33, 34, 35]
    ];

    const CURRENCY_MAP = [
        'R' => 'EUR',
        'D' => 'USD',
        'F' => 'CHF'
    ];


    protected $inputString = '';


    /**
     * @param $input
     * @return array
     */
    public function parseFromString($input)
    {
        $this->inputString = $input;
        $models = $this->parse();
        return $this->buildModels($models);
    }

    /**
     * @return array
     */
    protected function parse()
    {
        $delimiter = substr_count($this->inputString, "\r\n-") > substr_count($this->inputString,
            '@@-') ? "\r\n" : '@@';
        $sanitizedInput = preg_replace("/$delimiter(?!:)/ms", '', $this->inputString);


        $fields = $this->splitInputInFields($sanitizedInput, $delimiter);
        $transactions = $this->buildTransactionsFromFields($fields);


        $transactions = array_map(function ($transaction) {
            ['primary' => $primary, 'meta' => $meta] = $transaction;
            return array_merge($this->parsePrimary($primary), $this->parseMeta($meta));
        }, $transactions);

//        $transactions = $this->stripUnwantedFields($transactions); 

        return array_values($transactions);
    }

    /**
     * @param $input
     * @param $delimiter
     * @return array
     */
    protected function splitInputInFields($input, $delimiter)
    {
        $fields = explode($delimiter . ':', $input);
        $fields = array_filter($fields);
        return array_map(function ($element) {
            list($key, $value) = explode(':', $element, 2);
            return ['key' => $key, 'value' => $value];
        }, $fields);
    }

    /**
     * @param $fields
     * @return array
     */
    protected function buildTransactionsFromFields($fields)
    {
        $result = [];
        $currentTransaction = [];
        foreach ($fields as $field) {
            if ($field['key'] == 61) {
                $result[] = $currentTransaction;
                $currentTransaction['primary'] = $field['value'];
                $currentTransaction['meta'] = '';
            }
            if ($field['key'] == 86 && count($currentTransaction) > 0) {
                $currentTransaction['meta'] = $field['value'];
            }
        }
        $result[] = $currentTransaction;
        return array_filter($result);
    }

    /**
     * @param $primary
     * @return array
     */
    protected function parsePrimary($primary)
    {
        $primary = $this->normalizePrimary($primary);

        //in that case the booking is "canceled"
        if (mb_substr($primary, 10, 1) == 'R') {
            $primary = mb_substr($primary, 0, 10) . mb_substr($primary, 11);
        }

        $sign = mb_substr($primary, 10, 1);
        $sign = ['C' => 1, 'D' => -1][$sign];


        $currencyIdentifier = mb_substr($primary, 11, 1);


        $referenceContainer = mb_substr($primary, 12);

        list($amount, $preReference) = explode('N', $referenceContainer, 2);
        $amount = floatval(str_replace(',', '.', $amount)) * 100;
        $amount = intval(round($amount)) * $sign;

        $bookingKey = 'N' . mb_substr($preReference, 0, 3);
        $bookingReference = explode('//', mb_substr($preReference, 3))[0];

        return array_merge(
            $this->buildDates($primary),
            [
                'currency' => $this->buildCurrency($currencyIdentifier),
                'base_amount' => $amount,
                'booking_key' => $bookingKey,
                'booking_reference' => $bookingReference,
            ]
        );
    }

    /**
     * @param $meta
     * @return array
     */
    protected function parseMeta($meta)
    {
        $delimiter = mb_substr($meta, 3, 1);
        if (!$delimiter) {
            return ['transaction_code' => mb_substr($meta, 0, 3)];
        }
        $fields = explode($delimiter, $meta);

        $transactionCode = array_shift($fields);
        $references = $this->parseReferenceSubFields($fields);
        $other = $this->parseOtherSubfields($fields);


        return array_merge(
            ['transaction_code' => $transactionCode,],
            $references,
            $other
        );
    }


    /**
     * @param $primary
     * @return array
     */
    protected function buildDates($primary)
    {
        $valueDate = DateTime::createFromFormat('ymd', mb_substr($primary, 0, 6));

        $bookingDate = DateTime::createFromFormat('ymd', $valueDate->format('y') . mb_substr($primary, 6, 4));

        $diff = $valueDate->diff($bookingDate, false);

        if ($diff->days > 182 && $diff->invert == 1) {
            $bookingDate->modify('+1 year');
        }
        if ($diff->days > 182 && $diff->invert == 0) {
            $bookingDate->modify('-1 year');
        }
        return ['value_date' => $valueDate, 'booking_date' => $bookingDate];
    }


    /**
     * @param $fields
     * @return array
     */
    protected function parseReferenceSubFields($fields)
    {
        $breakPattern = "/^2\d([A-Z]{4})\+/";
        $sequencePattern = "/^2\d/";

        $match = [];
        $preResult = array_map(function () {
            return null;
        }, $this->descriptionMap);

        foreach ($fields as $field) {
            $currentMatch = [];
            if (preg_match($breakPattern, $field, $currentMatch)) {
                $match = $currentMatch;
                $preResult[$match[1]] = preg_replace($breakPattern, '', $field);
                continue;
            }
            if (preg_match($sequencePattern, $field) && array_key_exists(1, $match)) {
                $preResult[$match[1]] .= preg_replace($sequencePattern, '', $field);
                continue;
            }
        }

        $result = [];
        foreach ($preResult as $key => $item) {
            if (!array_key_exists($key, $this->descriptionMap)) {
                break;
            }
            $result[$this->descriptionMap[$key]] = $item;
        }

        $result = $this->postProcessReferenceSubfields($result, $fields);

        return $result;
    }

    /**
     * @param $primary
     * @return string
     */
    protected function normalizePrimary($primary)
    {
        //if no booking date is given -> set value date = booking date
        if (!preg_match('/\d{10}/', $primary)) {
            $primary = mb_substr($primary, 0, 4) . $primary;
        }

        //in that case the booking is "canceled"
        if (mb_substr($primary, 10, 1) == 'R') {
            $primary = mb_substr($primary, 0, 10) . mb_substr($primary, 11);
        }

        //check is currency identifier is present (take R for EUR)
        if (!preg_match('/[A-Z]/', mb_substr($primary, 11, 1))) {
            $primary = mb_substr($primary, 0, 11) . 'R' . mb_substr($primary, 11);
        }


        return $primary;
    }

    /**
     * @param $currencyIdentifier
     * @return mixed
     */
    protected function buildCurrency($currencyIdentifier)
    {
        if (array_key_exists($currencyIdentifier, static::CURRENCY_MAP)) {
            return static::CURRENCY_MAP[$currencyIdentifier];
        }
        return $currencyIdentifier;
    }

    /**
     * @param $fields
     * @return array
     */
    protected function parseOtherSubfields($fields)
    {
        return array_map(function ($patterns) use ($fields) {
            if (!is_array($patterns)) {
                $patterns = [$patterns];
            }

            $result = array_map(function ($pattern) use ($fields) {
                $pattern = "/^$pattern/";
                return preg_replace($pattern, '', $this->findFirst($pattern, $fields));
            }, $patterns);

            return implode($result);
        }, $this->fieldMap);
    }

    /**
     * @param array $models
     * @return array
     */
    protected function buildModels(array $models = [])
    {
        return array_map(function ($props) {
            return new Transaction($props);
        }, $models);
    }

    /**
     * @param $pattern
     * @param array $haystack
     * @return mixed|null
     */
    protected function findFirst($pattern, array $haystack = [])
    {
        foreach ($haystack as $field) {
            if (preg_match($pattern, $field)) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @param $transactions
     * @return array
     */
    protected function stripUnwantedFields($transactions)
    {
        return array_map(function($transaction) {
            return array_diff_key($transaction, array_flip($this->unwantedFields));        
        },$transactions);         
    }

    /**
     * @param $result
     * @param $fields
     * @return mixed
     */
    protected function postProcessReferenceSubfields($result, $fields)
    {
        $filledFields = array_filter($result, function ($field) {
            return !is_null($field);
        });
        if (count($filledFields) > 0) {
            return $result;
        }
        
        $pattern = "/^2\d/";
        $fields = array_map(function ($field) use ($pattern) {
            if (!preg_match($pattern, $field)) {
                return null;
            }
            return preg_replace($pattern, '', $field);
        }, $fields);

        $description = implode('', $fields);

        $result['description'] = $description ? $description : null;

        return $result;
    }
}
