<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Models\Account;
use Abiturma\PhpFints\Segments\HKKAZ;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HKKAZTest
 * @package Tests\Segments
 */
class HKKAZTest extends TestCase
{
    protected $account;

    public function setUp(): void
    {
        parent::setUp();

        $this->account = new Account([
            'iban' => 'myIban',
            'bic' => 'myBic',
            'bank_code' => 'myBankCode',
            'account_number' => 'myAccountNumber'
        ]);
    }

    #[Test]
    public function it_builds_a_segment_head()
    {
        $this->assertStringStartsWith('HKKAZ:1:6+', (new HKKAZ())->toString());
    }

    #[Test]
    public function version_6_can_be_built_from_an_account()
    {
        $hkkaz = (new HKKAZ())->fromAccount($this->account);
        $this->assertMatchesRegularExpression('/HKKAZ:1:6\+myAccountNumber:EUR:280:myBankCode\+N\+\d{8}\+\d{8}\+\+/', $hkkaz->toString());
    }

    #[Test]
    public function version_7_can_be_built_from_an_account()
    {
        $hkkaz = (new HKKAZ())->setVersion(7)->fromAccount($this->account);
        $this->assertMatchesRegularExpression('/HKKAZ:1:7\+myIban:myBic:myAccountNumber::280:myBankCode\+N\+\d{8}\+\d{8}\+\+/', $hkkaz->toString());
    }
}
