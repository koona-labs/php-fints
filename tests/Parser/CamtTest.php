<?php

namespace Abiturma\PhpFints\Tests\Parser;

use Abiturma\PhpFints\Models\Transaction;
use Abiturma\PhpFints\Parser\Camt;
use DateTime;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class CamtTest
 * @package Tests\Parser
 */
class CamtTest extends TestCase
{

    /** @test */
    public function it_returns_an_array_of_the_right_length()
    {
        $result = $this->parse();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Transaction::class, $result[0]);
    }
    
    /** @test */
    public function it_parses_the_booking_date_correctly()
    {
        $result = $this->parse()[0];
        $this->assertInstanceOf(DateTime::class, $result->booking_date);
        $this->assertEquals('2010-01-01', $result->booking_date->format('Y-m-d'));
    }
    
    /** @test */
    public function it_parses_the_value_date_correctly()
    {
        $result = $this->parse()[0];
        $this->assertInstanceOf(DateTime::class, $result->value_date);
        $this->assertEquals('2010-01-02', $result->value_date->format('Y-m-d'));
    }
    
    /** @test */
    public function it_parses_the_amount_correctly()
    {
        $amounts = array_map(function ($transaction) {
            return $transaction->base_amount;
        }, $this->parse());
        
        $this->assertEquals([-1234,5409], $amounts);
    }
    
    /** @test */
    public function it_parses_the_remote_correctly()
    {
        $names = array_map(function ($transaction) {
            return $transaction->remote_name;
        }, $this->parse());

        $accountNumbers = array_map(function ($transaction) {
            return $transaction->remote_account_number;
        }, $this->parse());
        

        $this->assertEquals(['Creditor 1','Debitor 2'], $names);
        $this->assertEquals(['DE11520513735120710131','DE11520513735120710131'], $accountNumbers);
    }
    
    /** @test */
    public function it_handles_prima_nota_and_transaction_codes_correctly()
    {
        $result = $this->parse()[0];
        $this->assertEquals('TC1', $result->transaction_code);
        $this->assertEquals('PN1', $result->prima_nota);
    }
    
    /** @test */
    public function it_handles_multiline_descriptions_correctly()
    {
        $result = $this->parse()[0];
        $this->assertEquals('Remittance 1 Line 1Remittance 1 Line 2', $result->description);
    }


    /**
     * @return array
     * @throws \Genkgo\Camt\Exception\ReaderException
     */
    protected function parse()
    {
        return (new Camt())->parseFromString($this->getXml());
    }


    /**
     * @return string
     */
    protected function getXml()
    {
        return <<<EOF
<?xml version='1.0' encoding='UTF-8' ?>
<Document xmlns='urn:iso:std:iso:20022:tech:xsd:camt.052.001.02'>
    <BkToCstmrAcctRpt>
        <GrpHdr>
            <MsgId>Message-Id</MsgId>
            <CreDtTm>2000-01-01T00:00:00.000000+01:00</CreDtTm>
        </GrpHdr>
        <Rpt>
            <Id>Transaction-Id</Id>
            <LglSeqNb>3</LglSeqNb>
            <CreDtTm>2010-01-01T00:00:00.00000+01:00</CreDtTm>
            <Acct>
                <Id>
                    <IBAN>DE27100777770209299700</IBAN>
                </Id>
                <Ccy>EUR</Ccy>
            </Acct>
            <Ntry>
                <Amt Ccy='EUR'>12.34</Amt>
                <CdtDbtInd>DBIT</CdtDbtInd>
                <Sts>BOOK</Sts>
                <BookgDt>
                    <Dt>2010-01-01</Dt>
                </BookgDt>
                <ValDt>
                    <Dt>2010-01-02</Dt>
                </ValDt>
                <AcctSvcrRef>AcctScvrRef1</AcctSvcrRef>
                <BkTxCd>
                    <Domn>
                        <Cd>Cd</Cd>
                        <Fmly>
                            <Cd>CD</Cd>
                            <SubFmlyCd>CD</SubFmlyCd>
                        </Fmly>
                    </Domn>
                    <Prtry>
                        <Cd>NDDT+TC1+PN1+992</Cd>
                    </Prtry>
                </BkTxCd>
                <NtryDtls>
                    <TxDtls>
                        <Refs>
                            <AcctSvcrRef>AcctScvrRef1</AcctSvcrRef>
                            <InstrId>InstrId1</InstrId>
                            <EndToEndId>EndToEnd1</EndToEndId>
                            <TxId>TxId1</TxId>
                            <MndtId>MndtId1</MndtId>
                        </Refs>
                        <RltdPties>
                            <Dbtr>
                                <Nm>Debitor 1</Nm>
                            </Dbtr>
                            <DbtrAcct>
                                <Id>
                                    <IBAN>DE27100777770209299700</IBAN>
                                </Id>
                            </DbtrAcct>
                            <Cdtr>
                                <Nm>Creditor 1</Nm>
                                <Id>
                                    <PrvtId>
                                        <Othr>
                                            <Id>Creditor1-Id</Id>
                                        </Othr>
                                    </PrvtId>
                                </Id>
                            </Cdtr>
                            <CdtrAcct>
                                <Id>
                                    <IBAN>DE11520513735120710131</IBAN>
                                </Id>
                            </CdtrAcct>
                        </RltdPties>
                        <RltdAgts>
                            <DbtrAgt>
                                <FinInstnId>
                                    <BIC>ESSEGB2L</BIC>
                                </FinInstnId>
                            </DbtrAgt>
                            <CdtrAgt>
                                <FinInstnId>
                                    <BIC>ESSEGB2L</BIC>
                                </FinInstnId>
                            </CdtrAgt>
                        </RltdAgts>
                        <RmtInf>
                            <Ustrd>Remittance 1 Line 1</Ustrd>
                            <Ustrd>Remittance 1 Line 2</Ustrd>
                        </RmtInf>
                    </TxDtls>
                </NtryDtls>
                <AddtlNtryInf>SEPA-Basislastschrift</AddtlNtryInf>
            </Ntry>
            <Ntry>
                <Amt Ccy='EUR'>54.09</Amt>
                <CdtDbtInd>CRDT</CdtDbtInd>
                <Sts>BOOK</Sts>
                <BookgDt>
                    <Dt>2015-01-01</Dt>
                </BookgDt>
                <ValDt>
                    <Dt>2015-01-21</Dt>
                </ValDt>
                <AcctSvcrRef>AcctSvcrRef</AcctSvcrRef>
                <BkTxCd>
                    <Domn>
                        <Cd>CD</Cd>
                        <Fmly>
                            <Cd>CD</Cd>
                            <SubFmlyCd>CD</SubFmlyCd>
                        </Fmly>
                    </Domn>
                    <Prtry>
                        <Cd>NDDT+TC2+PN2+992</Cd>
                    </Prtry>
                </BkTxCd>
                <NtryDtls>
                    <TxDtls>
                        <Refs>
                            <AcctSvcrRef>AcctSvcrRef2</AcctSvcrRef>
                            <InstrId>InstrId2</InstrId>
                        </Refs>
                        <RltdPties>
                            <Dbtr>
                                <Nm>Debitor 2</Nm>
                            </Dbtr>
                            <DbtrAcct>
                                <Id>
                                    <IBAN>DE11520513735120710131</IBAN>
                                </Id>
                            </DbtrAcct>
                            <Cdtr>
                                <Nm>Creditor 2</Nm>
                                <Id>
                                    <PrvtId>
                                        <Othr>
                                            <Id>creditor2-Id</Id>
                                        </Othr>
                                    </PrvtId>
                                </Id>
                            </Cdtr>
                            <CdtrAcct>
                                <Id>
                                    <IBAN>DE27100777770209299700</IBAN>
                                </Id>
                            </CdtrAcct>
                        </RltdPties>
                        <RltdAgts>
                            <DbtrAgt>
                                <FinInstnId>
                                    <BIC>ESSEGB2L</BIC>
                                </FinInstnId>
                            </DbtrAgt>
                            <CdtrAgt>
                                <FinInstnId>
                                    <BIC>ESSEGB2L</BIC>
                                </FinInstnId>
                            </CdtrAgt>
                        </RltdAgts>
                        <RmtInf>
                            <Ustrd>Remittance2</Ustrd>
                        </RmtInf>
                    </TxDtls>
                </NtryDtls>
                <AddtlNtryInf>SEPA-Basislastschrift</AddtlNtryInf>
            </Ntry>
        </Rpt>
    </BkToCstmrAcctRpt>
</Document>
EOF;
    }
}
