<?php

namespace Abiturma\PhpFints\Response;

use Abiturma\PhpFints\DataElements\DataElement;

/**
 * Class Feedback
 * @package Abiturma\PhpFints
 */
class Feedback
{
    /**
     * @var ResponseSegment
     */
    protected $segment;
    
    protected $reference = null;

    /**
     * Feedback constructor.
     * @param ResponseSegment $segment
     */
    public function __construct(ResponseSegment $segment)
    {
        $this->segment = $segment;
    }

    /**
     * @param $reference
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->segment->getElementAtPosition(2)->getElementAtPosition(1)->toRawValue();
    }

    /**
     * @param $code
     * @return DataElement|null
     */
    public function getElementByCode($code)
    {
        foreach ($this->segment->getElements() as $element) {
            if ($element->getElementAtPosition(1)->toRawValue() == $code) {
                return $element;
            }
        }
        return null;
    }

    /**
     * @return null
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return mixed
     */
    public function getPaginationToken()
    {
        return $this->segment->getElementAtPosition(2)->getElementAtPosition(4)->toRawValue();
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return array_map(function ($element) {
            return $element->getElementAtPosition(3)->toRawValue();
        }, $this->segment->getElements());
    }

    /**
     * @return string
     */
    public function getFullMessage()
    {
        return implode(' | ', $this->getMessages());
    }
}
