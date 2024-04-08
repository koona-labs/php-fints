<?php

namespace Abiturma\PhpFints\Tests\DataElements;

use Abiturma\PhpFints\DataElements\Bin;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class BinTest
 * @package Tests\DataElements
 */
class BinTest extends TestCase
{


    #[Test]
    public function a_string_is_encapsuled_in_the_bin_format()
    {
        $testString = 'testString';
        $this->assertEquals('@10@testString', (new Bin($testString))->toString());
    }

    #[Test]
    public function special_characters_are_not_escaped_within_bin_data_fields()
    {
        $testString = 'test?@string';
        $this->assertEquals('@12@test?@string', (new Bin($testString))->toString());
    }
}
