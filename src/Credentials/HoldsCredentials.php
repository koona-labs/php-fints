<?php

namespace Abiturma\PhpFints\Credentials;


interface HoldsCredentials
{

    public function host();

    public function port(); 

    public function bankCode(); 

    public function username(); 

    public function pin();
    

    public function setHost($host);

    public function setPort($port);

    public function setBankCode($bankCode);

    public function setUsername($username);

    public function setPin($pin);


}