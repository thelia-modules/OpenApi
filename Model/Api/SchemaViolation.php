<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class SchemaViolation.
 *
 * @OA\Schema(
 *     description="A schema violation"
 * )
 */
class SchemaViolation extends BaseApiModel
{
    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $message;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return SchemaViolation
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
