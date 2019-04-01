<?php

namespace Abiturma\PhpFints\Credentials;

/**
 * Class CredentialsContainer
 * @package Abiturma\PhpFints
 */
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

    /**
     * @return int
     */
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

    /**
     * @param $host
     * @return $this|mixed
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param $port
     * @return $this|mixed
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param $bankCode
     * @return $this|mixed
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    /**
     * @param $username
     * @return $this|mixed
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param $pin
     * @return $this|mixed
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
        return $this;
    }
}
