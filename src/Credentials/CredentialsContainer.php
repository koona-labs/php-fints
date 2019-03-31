<?php

namespace Abiturma\PhpFints\Credentials;


class CredentialsContainer implements HoldsCredentials
{
    
    protected $host; 
    
    protected $port = 443; 
    
    protected $bankCode; 
    
    protected $username; 
    
    protected $pin; 
    

    public function host()
    {
        return $this->host; 
    }

    public function port()
    {
        return $this->port; 
    }

    public function bankCode()
    {
        return $this->bankCode; 
    }

    public function username()
    {
        return $this->username; 
    }

    public function pin()
    {
        return $this->pin; 
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
        return $this;
    }
}