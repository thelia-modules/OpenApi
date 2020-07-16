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
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $key;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $error;

    public function __construct(
        $key = null,
        $error = null
    ) {
        parent::__construct();

        $this->key = $key;
        $this->error = $error;
    }

    /**
     * @return null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param null $key
     *
     * @return SchemaViolation
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param null $error
     *
     * @return SchemaViolation
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }
}