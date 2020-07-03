<?php

namespace OpenApi\Exception;

use OpenApi\Model\Api\Error;

class OpenApiException extends \Exception
{
    /** @var Error */
    protected $error;

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