<?php

namespace Osama\AlQaseh\Exceptions;

use Exception;

class AlQasehException extends Exception
{
    protected $errorCode;
    protected $referenceCode;

    /**
     * AlQasehException constructor.
     *
     * @param string $message
     * @param string $errorCode
     * @param string $referenceCode
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $errorCode = "", $referenceCode = "", $code = 0)
    {
        parent::__construct($message, $code);
        $this->errorCode = $errorCode;
        $this->referenceCode = $referenceCode;
    }

    /**
     * Get the error code.
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get the reference code.
     *
     * @return string
     */
    public function getReferenceCode()
    {
        return $this->referenceCode;
    }
}