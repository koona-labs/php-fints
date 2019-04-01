<?php

namespace Abiturma\PhpFints\Response;

use Abiturma\PhpFints\Encryption\EncryptsASequenceOfSegments;

/**
 * Class ResponseFactory
 * @package Abiturma\PhpFints
 */
class ResponseFactory
{
    protected $responseString;

    protected $savedBinaries = [];

    /**
     * @var EncryptsASequenceOfSegments
     */
    protected $encrypter;


    /**
     * ResponseFactory constructor.
     * @param EncryptsASequenceOfSegments $encrypter
     */
    public function __construct(EncryptsASequenceOfSegments $encrypter)
    {
        $this->encrypter = $encrypter;
    }


    /**
     * @param $responseString
     * @return mixed
     * @throws \Abiturma\PhpFints\Exceptions\ResponseSyntaxException
     */
    public function fromString($responseString)
    {
        $this->responseString = $responseString;
        $this->savedBinaries = [];
        return $this->parse();
    }

    /**
     * @param $base64String
     * @return mixed
     */
    public function fromBase64($base64String)
    {
        $decodedString = base64_decode($base64String);
        
        if (!mb_check_encoding($decodedString)) {
            $decodedString = iconv('ISO-8859-1', mb_internal_encoding(), $decodedString);
        }
        return $this->fromString($decodedString);
    }


    /**
     * @return mixed
     * @throws \Abiturma\PhpFints\Exceptions\ResponseSyntaxException
     */
    protected function parse()
    {
        $responseString = $this->responseString;
        $responseString = $this->handleBinaries($responseString);
        $rawSegments = $this->splitSegments($responseString);
        $segments = $this->buildSegments($rawSegments);

        return $this->isEncrypted($segments) ? $this->decrypt($segments) : Response::fromSegments($segments)->setRaw($this->responseString);
    }


    /**
     * @param $responseString
     * @return string
     */
    protected function handleBinaries($responseString)
    {
        $binaryPattern = "/([^\?]|^)(\?\?)*(@\d+@)/m";
        $matches = [];
        $key = 0;
        $savedBinaries = [];
        while ($this->mb_preg_match($binaryPattern, $responseString, $matches)) {
            list($lengthMarker, $position) = $matches[3];
            $length = (int)str_replace('@', '', $lengthMarker);
            $lengthOfLengthMarker = mb_strlen($lengthMarker);
            $savedBinaries[$key] = mb_substr($responseString, $position + $lengthOfLengthMarker, $length);
            $responseString = mb_substr($responseString, 0, $position) . "@#$key@" . mb_substr($responseString, $position + $length + $lengthOfLengthMarker);
            $key++;
        }
        $this->savedBinaries = $savedBinaries;
        return $responseString;
    }

    /**
     * @param $responseString
     * @return array[]|false|string[]
     */
    protected function splitSegments($responseString)
    {
        $segmentPattern = "/[^\?](\?\?)*\K'/";
        return preg_split($segmentPattern, $responseString);
    }

    /**
     * @param $rawSegments
     * @return array
     * @throws \Abiturma\PhpFints\Exceptions\ResponseSyntaxException
     */
    protected function buildSegments($rawSegments)
    {
        $result = [];
        foreach ($rawSegments as $segment) {
            if (!$segment) {
                continue;
            }
            $result[] = ResponseSegment::parseFromString($segment, $this->savedBinaries);
        }
        return $result;
    }

    /**
     * @param $segments
     * @return mixed
     */
    protected function decrypt($segments)
    {
        $response = $this->encrypter->decrypt($segments[2]->getElementAtPosition(2)->toRawValue());
        $head = $segments[0]->toString();
        $end = $segments[3]->toString();

        return $this->fromString($head . $response . $end);
    }

    /**
     * @param $segments
     * @return bool
     */
    protected function isEncrypted($segments)
    {
        if (count($segments) != 4) {
            return false;
        }

        $hkvsk = $segments[1];
        $hkvsd = $segments[2];

        if ($hkvsk->getType() != 'HNVSK' || $hkvsk->getSegmentNumber() != 998 || $hkvsd->getType() != 'HNVSD') {
            return false;
        }

        return true;
    }


    /**
     * @param $pattern
     * @param $subject
     * @param $matches
     * @return false|int
     */
    protected function mb_preg_match($pattern, $subject, &$matches)
    {
        $hasMatch = preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
        if ($hasMatch) {
            foreach ($matches as &$ha_match) {
                $ha_match[1] = mb_strlen(substr($subject, 0, $ha_match[1]));
            }
        }

        return $hasMatch;
    }
}
