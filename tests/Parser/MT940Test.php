<?php

namespace Abiturma\PhpFints\Tests\Parser;

use Abiturma\PhpFints\Models\Transaction;
use Abiturma\PhpFints\Parser\MT940;
use DateTime;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class MT940Test
 * @package Tests\Parser
 */
class MT940Test extends TestCase
{


    /** @test */
    public function it_returns_an_array_of_the_right_length_and_type()
    {
        $result = $this->parse([
            $this->makeStandardTransaction(),
            $this->makeStandardTransaction(),
            $this->makeStandardTransaction()
        ]);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Transaction::class, $result[0]);
    }

    /** @test */
    public function it_parses_a_value_date()
    {
        $result = $this->parse($this->makeStandardTransaction())[0];
        $this->assertInstanceOf(DateTime::class, $result->value_date);
        $this->assertEquals('2019-12-15', $result->value_date->format('Y-m-d'));
    }

    /** @test */
    public function it_picks_the_right_year_for_the_booking_date()
    {
        $result = $this->parse($this->makeStandardTransaction())[0];
        $this->assertInstanceOf(DateTime::class, $result->booking_date);
        $this->assertEquals('2020-01-02', $result->booking_date->format('Y-m-d'));
        $result = $this->parse(":61:1001021220DR512,00NMSCNONREF@@")[0];
        $this->assertInstanceOf(DateTime::class, $result->booking_date);
        $this->assertEquals('2009-12-20', $result->booking_date->format('Y-m-d'));
    }

    /** @test */
    public function it_parses_multiline_description_correctly()
    {
        $result = $this->parse($this->makeStandardTransaction())[0];
        $this->assertEquals("HERE:SOME:DESCRIPTION:AND:MORE:DESCRIPTION", $result->description);
    }

    /** @test */
    public function it_parses_end_to_end_reference_correctly()
    {
        $result = $this->parse($this->makeStandardTransaction())[0];
        $this->assertEquals('testEndToEndReference', $result->end_to_end_reference);
    }

    /** @test */
    public function if_no_end_to_end_reference_is_provided_the_value_is_null()
    {
        $testString = $this->buildTestString(":61:1912150102DR512,00NMSCNONREF@@:86:105?00SEPA-Basislastschrift?10testPrimaNota?20SVWZ+SomeDescription?30remoteBankCode?31SomeIban?32A REMOTE NAME@@");
        $result = $this->parse($testString);
        $result = $result[0];
        $this->assertNull($result->end_to_end_reference);
    }

    /** @test */
    public function it_reads_prima_nota_and_remote_account_correctly()
    {
        $result = $this->parse($this->makeStandardTransaction())[0];
        $this->assertEquals('testPrimaNota', $result->prima_nota);
        $this->assertEquals('remoteBankCode', $result->remote_bank_code);
        $this->assertEquals('SomeIban', $result->remote_account_number);
        $this->assertEquals('A REMOTE NAME', $result->remote_name);
    }

    /** @test */
    public function it_parses_debit_and_credit_correctly()
    {
        $result = $this->parse($this->buildTestString(":61:1912150102CR512,00NMSCNONREF@@"))[0];
        $this->assertGreaterThan(0, $result->base_amount);
        $result = $this->parse($this->buildTestString(":61:1912150102DR512,00NMSCNONREF@@"))[0];
        $this->assertLessThan(0, $result->base_amount);
    }

    /** @test */
    public function it_parses_amount_correctly()
    {
        $result = $this->parse($this->buildTestString(":61:1912150102CR,12NMSCNONREF@@"))[0];
        $this->assertEquals(12, $result->base_amount);
        $result = $this->parse($this->buildTestString(":61:1912150102CR12,34NMSCNONREF@@"))[0];
        $this->assertEquals(1234, $result->base_amount);
    }


    /** @test */
    public function it_recognizes_different_formats()
    {
        $testString = ":20:STARTUMS@@:25:bankCode/account@@:28C:0@@:60F:C000101EUR300,09@@:61:190318D330,34NMSC@@"
            . ":86:105?00EINZUGSERMAECHTIGUNG?10931?20MREF+Some Ref @@?21CRED+SomeIban "
            . "?22SVWZ+Description Part 1@@?23Part2"
            . "?24Part3 MREF: mref CR@@?25ED: cred IBAN?26 iban BI@@"
            . "?27C: bic ?30SOMEBIC?31SomeIban@@"
            . "?32SomeName?34992@@:62F:C000101EUR5000,75@@-";
        $testString = str_replace("@@", "\r\n", $testString);
        $result = $this->parse($testString);
        $this->assertCount(1, $result);
        $this->assertEquals($result[0]->description, 'Description Part 1Part2Part3 MREF: mref CRED: cred IBAN iban BIC: bic ');
        $this->assertEquals($result[0]->prima_nota, 931);
        $this->assertEquals($result[0]->base_amount, -33034);
    }


    /**
     * @param array $transactions
     * @return string
     */
    protected function buildTestString($transactions = [])
    {
        if (!is_array($transactions)) {
            $transactions = [$transactions];
        }
        $start = "@@:20:STARTUMS@@:25:12345678/123456789@@:28C:3/1@@:60F:C15674EUR54976,93@@";
        return $start . implode($transactions);
    }

    /**
     * @return string
     */
    protected function makeStandardTransaction()
    {
        return ":61:1912150102DR512,00NMSCNONREF@@" .
            ":86:105?00SEPA-Basislastschrift?10testPrimaNota?20EREF+testEndToEndReference?21KREF+UV3@@SOME-MORE-STUFF?" .
            "test-test-test.?23MREF+some-m-ref?24CRED+some@@cred-ref?12345678" .
            "?26SVWZ+HERE:SOME:DESCRIPTION:?2@@8AND:MORE:DESCRIPTION?30remoteBankCode?31SomeIban?32A REMOTE NAME@@";
    }

    /**
     * @param $transactions
     * @return array
     */
    protected function parse($transactions)
    {
        $testString = $this->buildTestString($transactions);
        return (new MT940())->parseFromString($testString);
    }
}
