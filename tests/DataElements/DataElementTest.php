<?php

namespace Abiturma\PhpFints\Tests\DataElements;

use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class DataElementTest
 * @package Tests\DataElements
 */
class DataElementTest extends TestCase
{


    #[Test]
    public function it_wraps_a_value_and_parses_it_as_string()
    {
        $this->assertIsString((new DataElement(12))->toString());
    }

    #[Test]
    public function it_escapes_control_characters()
    {
        $testString = "@bla??bla'hack+";
        $this->assertEquals("?@bla????bla?'hack?+", (new DataElement($testString))->toString());
    }

    #[Test]
    public function it_allows_for_fixed_length_values()
    {
        $testString = "1234";
        $this->assertEquals("001234", (new DataElement($testString))->fixedLength(6)->toString());
    }

    #[Test]
    public function it_transforms_an_array_into_an_array_of_data_elements()
    {
        $testArray = [1,2];
        $elementArray = DataElement::fromArray($testArray);
        $this->assertIsArray($elementArray);
        $this->assertInstanceOf(DataElement::class, $elementArray[0]);
        $this->assertEquals("1", $elementArray[0]->toString());
    }

    #[Test]
    public function it_is_clonable()
    {
        $original = (new DataElement(5))->fixedLength(12);
        $cloned = $original->clone();
        $original->fixedLength(20);
        $this->assertEquals('000000000005', $cloned->toString());
    }
}
