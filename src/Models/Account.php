<?php

namespace Abiturma\PhpFints\Models;

use Abiturma\PhpFints\DataElements\Groups\Kik;
use Abiturma\PhpFints\DataElements\Groups\Kti;
use Abiturma\PhpFints\DataElements\Groups\Ktv;
use Abiturma\PhpFints\DataElements\Groups\Ktz;

/**
 * Class Account
 * @package Abiturma\PhpFints
 */
class Account extends AbstractModel implements HasAccountStatement
{

    /**
     * @param Ktz $ktz
     * @return Account
     */
    public static function fromKtz(Ktz $ktz)
    {
        $attributes = [
            'iban' => $ktz->getElementAtPosition(2)->toRawValue(),
            'bic' => $ktz->getElementAtPosition(3)->toRawValue(),
            'account_number' => $ktz->getElementAtPosition(4)->toRawValue(),
            'bank_code' => $ktz->getBankCode()->toRawValue()
        ];

        return new static($attributes);
    }


    /**
     * @return $this
     */
    public function toFinTsAccount()
    {
        return $this;
    }

    /**
     * @return Ktv
     */
    public function toKtv()
    {
        return (new Ktv())
            ->setElementAtPosition(1, $this->account_number)
            ->setElementAtPosition(3, (new Kik())->setBankCode($this->bank_code));
    }

    /**
     * @return Kti
     */
    public function toKti()
    {
        return (new Kti())
            ->setIban($this->iban)
            ->setBic($this->bic)
            ->setAccountNumber($this->account_number)
            ->setKik((new Kik())->setBankCode($this->bank_code));
    }
}
