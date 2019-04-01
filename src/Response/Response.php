<?php

namespace Abiturma\PhpFints\Response;

use Abiturma\PhpFints\Response\Messages\AccountsResponse;
use Abiturma\PhpFints\Response\Messages\StatementOfAccount;
use Abiturma\PhpFints\Response\Messages\SyncResponse;

/**
 * Class Response
 * @package Abiturma\PhpFints
 */
class Response implements HoldsDialogParameters
{
    protected $segments = [];

    protected $originalOrder = [];

    protected $raw = '';


    /**
     * @param $raw
     * @return $this
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * @return string
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param $segments
     * @return $this
     */
    protected function setSegments($segments)
    {
        $this->segments = $segments;
        return $this;
    }

    /**
     * @param array $segments
     * @return Response
     */
    public static function fromSegments(array $segments)
    {
        return (new static)->setSegments($segments);
    }

    /**
     * @param array $order
     * @return $this
     */
    public function setOriginalOrder(array $order)
    {
        $this->originalOrder = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function getOriginalOrder()
    {
        return $this->originalOrder;
    }

    /**
     * @param $type
     * @return mixed|null
     */
    public function getFirstOfType($type)
    {
        $all = $this->getByType($type);
        return count($all) > 0 ? $all[count($all) - 1] : null;
    }

    /**
     * @param $type
     * @return array
     */
    public function getByType($type)
    {
        $type = mb_strtoupper($type);
        $result = array_filter(
            $this->segments,
            function ($segment) use ($type) {
                return $segment->getType() == $type;
            }
        );
        return array_values($result);
    }

    /**
     * @return Feedback|null
     */
    public function getGeneralFeedback()
    {
        $hirmg = $this->getFirstOfType('HIRMG');
        return $hirmg ? new Feedback($hirmg) : null;
    }

    /**
     * @return array
     */
    public function getSegmentalFeedback()
    {
        return array_map(function ($segment) {
            $relationNumber = $segment->getRelationNumber();
            $referenceSegment = array_key_exists($relationNumber, $this->originalOrder) ?
                $this->originalOrder[$relationNumber] : null;
            return (new Feedback($segment))->setReference($referenceSegment);
        }, $this->getByType('HIRMS'));
    }

    /**
     * @return string
     */
    public function getFullErrorMessage()
    {
        $result = $this->getGeneralFeedback()->getFullMessage();
        $result .= "|||" . implode('||', array_map(function ($feedback) {
            return $feedback->getFullMessage();
        }, $this->getSegmentalFeedback()));
        return $result;
    }

    /**
     * @return mixed
     */
    public function getDialogId()
    {
        return $this->getFirstOfType('HNHBK')->getElementAtPosition(4)->toRawValue();
    }

    /**
     * @return SyncResponse
     */
    public function sync()
    {
        return new SyncResponse($this);
    }

    /**
     * @return AccountsResponse
     */
    public function accounts()
    {
        return new AccountsResponse($this);
    }

    /**
     * @return StatementOfAccount
     */
    public function statementOfAccount()
    {
        return new StatementOfAccount($this);
    }

    /**
     * @param $segmentName
     * @return Feedback|null
     */
    public function getFeedbackBySegmentName($segmentName)
    {
        $segmentName = strtoupper($segmentName);
        $result = array_filter($this->getSegmentalFeedback(), function ($feedback) use ($segmentName) {
            return $feedback->getReference() == $segmentName;
        });

        $result = array_values($result);

        return count($result) > 0 ? $result[0] : null;
    }

    /**
     * @param $segmentName
     * @return bool
     */
    public function isPaginated($segmentName)
    {
        $feedback = $this->getFeedbackBySegmentName($segmentName);
        return $feedback && $feedback->getCode() == 3040;
    }

    /**
     * @param $segmentName
     * @return string|null
     */
    public function getPaginationToken($segmentName)
    {
        if (!$this->isPaginated($segmentName)) {
            return null;
        }
        return $this->getFeedbackBySegmentName($segmentName)->getPaginationToken();
    }


    /**
     * @return bool
     */
    public function isOk()
    {
        if (!$this->getGeneralFeedback()) {
            return false;
        }
        return substr($this->getGeneralFeedback()->getCode(), 0, 1) != 9;
    }

    /**
     * @return bool
     */
    public function hasWarnings()
    {
        if (!$this->getGeneralFeedback()) {
            return false;
        }
        return substr($this->getGeneralFeedback()->getCode(), 0, 1) == 3;
    }

    /**
     * @return array
     */
    public function toMergableParameters()
    {
        if ($this->isOk()) {
            return ['dialogId' => $this->getDialogId()];
        }
        
        return [];
    }
}
