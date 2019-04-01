<?php

namespace Abiturma\PhpFints\Credentials;

/**
 * Interface HoldsCredentials
 * @package Abiturma\PhpFints
 */
interface HoldsCredentials
{
    public function host();

    public function port();

    public function bankCode();

    public function username();

    public function pin();


    /**
     * @param $host
     * @return $this
     */
    public function setHost($host);

    /**
     * @param $port
     * @return $this
     */
    public function setPort($port);

    /**
     * @param $bankCode
     * @return $this
     */
    public function setBankCode($bankCode);

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username);

    /**
     * @param $pin
     * @return $this
     */
    public function setPin($pin);
}
