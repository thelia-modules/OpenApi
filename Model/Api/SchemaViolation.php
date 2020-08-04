<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class SchemaViolation
 * @package OpenApi\Model\Api
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
    protected $key;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $error;

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return SchemaViolation
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return SchemaViolation
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }
}