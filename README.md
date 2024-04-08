# Php-Fints

[![Latest Stable Version](https://poser.pugx.org/abiturma/php-fints/v/stable)](https://packagist.org/packages/abiturma/php-fints)
![Tests](https://github.com/koona-labs/php-fints/actions/workflows/Tests.yml/badge.svg?branch=master)
[![License](https://poser.pugx.org/abiturma/php-fints/license)](https://packagist.org/packages/abiturma/php-fints)
[![composer.lock](https://poser.pugx.org/abiturma/php-fints/composerlock)](https://packagist.org/packages/abiturma/php-fints)


This package is a library to use an Hbci connection to your bank in order to retrieve statements of your bank accounts. 

## Installation

Available through composer:

`composer require abiturma/php-fints`

## Usage

### Initialization
First you have to create an instance of the Fints class by providing your credentials
```
use Abiturma\PhpFints\Fints

...

$fints = Fints::host('https://url-to-your-hbci-endpoint')
    ->bankCode('yourBankCode)
    ->username('yourUsername')
    ->pin('your-secret-pin'); 

```
The port of the connection defaults to 443, optionally you can change it by calling 
`->port('yourPort')`
on `$fints`. The interface is fluent. In particular the order of calling the different initialization methods does not matter. 

### Retrieving a list of your (sepa) accounts

Once `Fints` is initialized, you can get a list of your accounts calling `$fints->getAccounts()`. This method returns an array of your bank accounts, i.e. an array of instances of `Abiturma\PhpFints\Models\Account`, which behave similar to laravel model classes. 
In particular you can query the account data by calling magic getters like so: `$account->iban`, `$account->bank_code`, ... 
Alternatively you can call `->toArray()` on an account, to get an associative array of its attributes. 

### Retrieving the statement of a bank account
For a specific account, you can get a list of all transactions by calling `$fints->getStatementOfAccount($account)`. 
Optionally you can pass two `DateTime` objects to restrict the transactions to a specific date range:
`$fints->getStatementOfAccount($account, $from, $to)`. The result is an array of objects of type `Abiturma\PhpFints\Models\Transaction`, which behave similar to account models. In particular you can retrieve a list of all attributes by calling `->toArray()`

Among others, the following attributes are stored on the transaction model: 

* `base_amount` the signed integer value of the transactions in cents (e.g. -120 means -1.20€)
* `amount` the signed float value of the transaction (e.g. -1.20)
* `remote_bank_code` the BIC of the remote account
* `remote_name` the name of the creditor/debitor
* `remote_account_number` the IBAN of the remote_account
* `date` the booking date of the transaction
* `value_date` the value (or valuta) date of the transaction
* `description`
* `end_to_end_reference`
* `prima_nota`

## SWIFT vs. Camt

By default, `Fints` tries to get statements in the Camt format, if possible. Sometimes it might occur, that this leads to thrown exceptions while parsing the response. In that case it might be useful to specify the response format explicitly. For that reason `Fints` exposes the methods `getSwiftStatementOfAccount` and `getCamtStatementOfAccount`, which have the same signature as `getStatementOfAccount`  


## Customization and Integration 

### Credentials 
It can be quite cumbersome to provide all credentials each time you use `Fints`. For that reason it is possible to create your own implementation of a credentials repository, which for example could makes use of a config storage. In that case, build your implementation `$credentials` of the interface `Abiturma\PhpFints\Credentials\HoldsCredentials` and register it: 
````
use Abiturma\PhpFints\Credentials\HoldsCredentials
use Abiturma\PhpFints\Fints

class MyCredentials implements HoldsCredentials {

    public function host() {
        ...
    }
    
    public function setHost($host) {
        ...
    }
    
    ...

}

$credentials = new MyCredentials()

$fints = Fints::useCredentials($credentials)->...
````




### Logging
By default outbound and inbound messages are not logged. You can enable logging by registering an implementation of `Psr\LoggerInterface` like so: 
````
use Psr\LoggerInterface
use Abiturma\PhpFints\Fints

class MyLogger implements LoggerInterface {

    public function debug() {
       ...
    }
    
    ...

}

$logger = new MyLogger(); 

$fints = Fints::withLogger($logger)->host(...)->...
````

### Accounts
If you have your account data (i.e. iban, bic, account number, bank code), it is not necessary to call `->getAccounts()` first in order to get a statement. Instead you can just create an account instance on the fly: 

````
use Abiturma\PhpFints\Fints
use Abiturma\PhpFints\Models\Account

$account = new Account([
    'iban' => 'yourIban',
    'bic' => 'yourBic',
    'account_number' => 'yourAccountNumber',
    'bank_code' => 'yourBankCode'
])

$transactions = Fints::host(...)
    ->...
    ->getStatementOfAccount($account)

````

If you have your own account model it can act as a valid first parameter of `->getStatementOfAccount` as long as it implements the interface `Abiturma\PhpFints\Models\IdentifiesBankAccount` which demands a method `->getAccountAttributes()` which is supposed to return an associative array with the following keys: `iban, bic, account_number, bank_code`. 

## Compatibility

This library is work in progress and tested only with two banks so far. You can find the list of compatible banks in COMPATIBILITY.md. 
I'm looking forward to your contribution to make this list longer.  

## Acknowledgments

This project is heavily inspired by [fints-hbci-php](https://github.com/mschindler83/fints-hbci-php) by [Markus Schindler](https://github.com/mschindler83).


