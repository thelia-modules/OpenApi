<?php

namespace OpenApi\Exception;

use OpenApi\Model\Api\Error;

class OpenApiException extends \Exception
{
    /** @var string */
    protected $httpCode;

    /** @var Error */
    protected $error;

    public function __construct(Error $error, $httpCode = 400)
    {
        $this->error = $error;
        $this->httpCode = $httpCode;
    }

    /**
     * @return string
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param string $httpCode
     *
     * @return OpenApiException
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    /**
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param Error $error
     *
     * @return OpenApiException
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }
}
