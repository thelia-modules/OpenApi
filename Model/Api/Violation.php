<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class Violations
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A violations (part of an error)"
 * )
 */
class Violation extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $label;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $error;

    public function __construct(
        $label = null,
        $error = null
    ) {
        $this->label = $label;
        $this->error = $error;
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param null $label
     *
     * @return Violation
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
     * @return Violation
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }
}