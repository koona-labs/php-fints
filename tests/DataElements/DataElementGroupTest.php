<?php

namespace Abiturma\PhpFints\Tests\DataElements;

use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\DataElements\DataElementGroup;
use Abiturma\PhpFints\DataElements\Groups\Kik;
use Abiturma\PhpFints\Tests\TestCase;

/**
 * Class DataElementGroupTest
 * @package Tests\DataElements
 */
class DataElementGroupTest extends TestCase
{

    
    /** @test */
    public function it_takes_several_values_and_glues_them_as_string()
    {
        $group = new DataElementGroup();
        $group->addElement(12)->addElement(new DataElement(15))->addElement('my+test');
        $this->assertEquals("12:15:my?+test", $group->toString());
    }
    
    /** @test */
    public function it_returns_content_of_a_specific_position()
    {
        $group = new DataElementGroup();
        $group->addElement(3)->addElement(5)->addElement(7);
        $this->assertEquals("5", $group->getElementAtPosition(2)->toString());
    }
    
    /** @test */
    public function it_lets_you_modify_the_value_at_a_specific_position()
    {
        $group = new DataElementGroup();
        $group->addElement(3)->addElement(5)->addElement(7);
        $group->setElementAtPosition(2, 100);
        $this->assertEquals("3:100:7", $group->toString());
    }
    
    /** @test */
    public function groups_can_be_nested()
    {
        $nestedGroup = new DataElementGroup();
        $nestedGroup->addElement(1)->addElement(2);
        $ambientGroup = new DataElementGroup();
        $ambientGroup->addElement('start')->addElement($nestedGroup)->addElement('end');
        $this->assertEquals("start:1:2:end", $ambientGroup->toString());
    }
    
    /** @test */
    public function it_can_be_cloned()
    {
        $original = (new DataElementGroup())->addElement(1)->addElement(2);
        $original->getElementAtPosition(2)->fixedLength(2);
        $clone = $original->clone();
        $original->getElementAtPosition(2)->fixedLength(3);
        $original->addElement(3);
        $this->assertEquals('1:002:3', $original->toString());
        $this->assertEquals('1:02', $clone->toString());
    }
    
    /** @test */
    public function a_group_can_be_transformed_to_another_class()
    {
        $dataElementGroup = (new DataElementGroup())->addElement(1)->addElement(2);
        $kik = Kik::fromDataElementGroup($dataElementGroup);
        $this->assertInstanceOf(Kik::class, $kik);
        $this->assertEquals('1:2', $kik->toString());
    }
}
