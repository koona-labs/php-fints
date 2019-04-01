<?php

namespace Abiturma\PhpFints\Misc;

use Abiturma\PhpFints\DataElements\DataElement;
use Abiturma\PhpFints\DataElements\HoldsStringableData;

/**
 * Trait HoldsDataElements
 * @package Abiturma\PhpFints
 */
trait HoldsDataElements
{
    protected $elements = [];


    /**
     * @param $element
     * @return $this
     */
    public function addElement($element)
    {
        $this->elements[] = $this->normalizeDataElement($element);
        return $this;
    }

    /**
     * @param $position
     * @param $element
     * @return $this
     */
    public function setElementAtPosition($position, $element)
    {
        $this->elements[$position-$this->getPositionOffset()] = $this->normalizeDataElement($element);
        return $this;
    }

    /**
     * @param $position
     * @return DataElement
     */
    public function getElementAtPosition($position)
    {
        if (array_key_exists($position-$this->getPositionOffset(), $this->elements)) {
            return $this->elements[$position-$this->getPositionOffset()];
        }
        return new DataElement('');
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param array $elements
     * @return $this
     */
    public function setElements(array $elements)
    {
        $this->elements = $elements;
        return $this;
    }


    /**
     * @param $position
     * @return $this
     */
    protected function removeElementAtPosition($position)
    {
        if (array_key_exists($position-$this->getPositionOffset(), $this->elements)) {
            unset($this->elements[$position-$this->getPositionOffset()]);
        }
        return $this;
    }

    /**
     * @param $element
     * @return HoldsStringableData
     */
    protected function normalizeDataElement($element)
    {
        if ($element instanceof HoldsStringableData) {
            return $element;
        }

        return new DataElement($element);
    }

    /**
     * @return int
     */
    protected function getPositionOffset()
    {
        return 1;
    }
}
