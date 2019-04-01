<?php

namespace Abiturma\PhpFints\Response;


use Abiturma\PhpFints\Response\Messages\AccountsResponse;
use Abiturma\PhpFints\Response\Messages\StatementOfAccount;
use Abiturma\PhpFints\Response\Messages\SyncResponse;

class Response implements HoldsDialogParameters
{

    protected $segments = [];

    protected $originalOrder = [];

    protected $raw = '';


    public function setRaw($raw)
    {
        $this->raw = $raw;
        return $this;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getSegments()
    {
        return $this->segments;
    }

    protected function setSegments($segments)
    {
        $this->segments = $segments;
        return $this;
    }

    public static function fromSegments(array $segments)
    {
        return (new static)->setSegments($segments);
    }

    public function setOriginalOrder(array $order)
    {
        $this->originalOrder = $order;
        return $this;
    }

    public function getOriginalOrder()
    {
        return $this->originalOrder;
    }

    public function getFirstOfType($type)
    {
        $all = $this->getByType($type);
        return count($all) > 0 ? $all[count($all) - 1] : null;
    }

    public function getByType($type)
    {
        $type = mb_strtoupper($type);
        $result = array_filter($this->segments,
            function ($segment) use ($type) {
                return $segment->getType() == $type;
            });
        return array_values($result);
    }

    public function getGeneralFeedback()
    {
        $hirmg = $this->getFirstOfType('HIRMG');
        return $hirmg ? new Feedback($hirmg) : null;
    }

    public function getSegmentalFeedback()
    {
        return array_map(function ($segment) {
            $relationNumber = $segment->getRelationNumber();
            $referenceSegment = array_key_exists($relationNumber, $this->originalOrder) ?
                $this->originalOrder[$relationNumber] : null;
            return (new Feedback($segment))->setReference($referenceSegment);
        }, $this->getByType('HIRMS'));
    }

    public function getFullErrorMessage()
    {
        $result = $this->getGeneralFeedback()->getFullMessage();
        $result .= "|||" . implode('||', array_map(function ($feedback) {
                return $feedback->getFullMessage();
            }, $this->getSegmentalFeedback()));
        return $result;
    }

    public function getDialogId()
    {
        return $this->getFirstOfType('HNHBK')->getElementAtPosition(4)->toRawValue();
    }

    public function sync()
    {
        return new SyncResponse($this);
    }

    public function accounts()
    {
        return new AccountsResponse($this);
    }

    public function statementOfAccount()
    {
        return new StatementOfAccount($this);
    }

    public function getFeedbackBySegmentName($segmentName)
    {
        $segmentName = strtoupper($segmentName);
        $result = array_filter($this->getSegmentalFeedback(), function ($feedback) use ($segmentName) {
            return $feedback->getReference() == $segmentName;
        });

        $result = array_values($result);

        return count($result) > 0 ? $result[0] : null;
    }

    public function isPaginated($segmentName)
    {
        $feedback = $this->getFeedbackBySegmentName($segmentName);
        return $feedback && $feedback->getCode() == 3040;
    }

    public function getPaginationToken($segmentName)
    {
        if (!$this->isPaginated($segmentName)) {
            return null;
        }
        return $this->getFeedbackBySegmentName($segmentName)->getPaginationToken();
    }


    public function isOk()
    {
        if (!$this->getGeneralFeedback()) {
            return false;
        }
        return substr($this->getGeneralFeedback()->getCode(), 0, 1) != 9;
    }

    public function hasWarnings()
    {
        if (!$this->getGeneralFeedback()) {
            return false;
        }
        return substr($this->getGeneralFeedback()->getCode(), 0, 1) == 3;
    }

    public function toMergableParameters()
    {
        if ($this->isOk()) {
            return ['dialogId' => $this->getDialogId()];
        }
        
        return []; 
    }

}