<?php

namespace Tests\Response;

use Abiturma\PhpFints\DataElements\Bin;
use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\DataElements\DataElementGroup;
use Abiturma\PhpFints\Response\ResponseSegment;
use Tests\TestCase;

/**
 * Class ResponseSegmentTest
 * @package Tests\Response
 */
class ResponseSegmentTest extends TestCase
{

    /** @test */
    public function a_segment_is_built_from_a_string()
    {
        $testString = 'HKTEST:1:2:4+Test1+Test2:2';
        $this->assertEquals('HKTEST', ResponseSegment::parseFromString($testString)->getType());
    }
    
    /** @test */
    public function it_parses_simple_data_elements()
    {
        $testString = 'HKTEST:1:2:4+Test1+Test2:2';
        $segment = ResponseSegment::parseFromString($testString);
        $this->assertInstanceOf(DataElement::class, $segment->getElementAtPosition(2));
        $this->assertEquals('Test1', $segment->getElementAtPosition(2)->toString());
    }
    
    /** @test */
    public function it_parses_nested_data_element_groups()
    {
        $testString = 'HKTEST:1:2:4+Test1+Test2:2';
        $segment = ResponseSegment::parseFromString($testString);
        $this->assertInstanceOf(DataElementGroup::class, $segment->getElementAtPosition(3));
        $this->assertEquals('Test2', $segment->getElementAtPosition(3)->getElementAtPosition(1)->toString());
    }
    
    /** @test */
    public function it_splits_only_if_the_outer_delimiter_is_not_escaped()
    {
        $testString = 'HKTEST:1:2:4+Test1?+Test2';
        $segment = ResponseSegment::parseFromString($testString);
        $this->assertEquals('Test1+Test2', $segment->getElementAtPosition(2)->toRawValue());
    }
    
    
    /** @test */
    public function it_unescapes_inner_values_correctly()
    {
        $testString = 'HKTEST:1:2:4+?@Test1+Test2:2';
        $segment = ResponseSegment::parseFromString($testString);
        $this->assertEquals('@Test1', $segment->getElementAtPosition(2)->toRawValue());
    }

    /** @test */
    public function it_splits_inner_values_only_if_the_inner_delimiter_is_not_escaped()
    {
        $testString = 'HKTEST:1:2:4+Test1:Test2?:Test2:Test3??:Test4';
        $segment = ResponseSegment::parseFromString($testString);
        $group = $segment->getElementAtPosition(2);
        $this->assertEquals('Test1', $group->getElementAtPosition(1)->toRawValue());
        $this->assertEquals('Test2:Test2', $group->getElementAtPosition(2)->toRawValue());
        $this->assertEquals('Test3?', $group->getElementAtPosition(3)->toRawValue());
        $this->assertEquals('Test4', $group->getElementAtPosition(4)->toRawValue());
    }
    
    
    /** @test */
    public function it_handles_binaries_correctly()
    {
        $testString = 'HKTEST:1:2:4+@#12@';
        $binaries = [12 => 'MyBinary@@@Test'];
        $segment = ResponseSegment::parseFromString($testString, $binaries);
        $this->assertInstanceOf(Bin::class, $segment->getElementAtPosition(2));
        $this->assertEquals('@15@MyBinary@@@Test', $segment->getElementAtPosition(2)->toString());
    }
    
    /** @test */
    public function it_handles_empty_fields_correctly()
    {
        $testString = 'HKTEST:1:2:4+Test1:Test2??:::Test4';
        $segment = ResponseSegment::parseFromString($testString);
        $this->assertEquals('', $segment->getElementAtPosition(2)->getElementAtPosition(3)->toString());
        $this->assertEquals('', $segment->getElementAtPosition(2)->getElementAtPosition(4)->toString());
    }
}
