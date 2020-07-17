<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;

/**
 * Class Error
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An error"
 * )
 */
class Error extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $description;

    /**
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/SchemaViolation"
     *     )
     * )
     */
    protected $schemaViolations;

    /**
     * @return null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null $title
     *
     * @return Error
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     *
     * @return Error
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /** @return array */
    public function getSchemaViolations()
    {
        return $this->schemaViolations;
    }

    /**
     * @param $schemaViolations SchemaViolation[]
     *
     * @return Error
     */
    public function setSchemaViolations($schemaViolations)
    {
        $this->schemaViolations = $schemaViolations;

        return $this;
    }

    /**
     * @param $schemaViolation SchemaViolation
     *
     * @return $this
     */
    public function appendViolation($schemaViolation)
    {
        $this->schemaViolations[] = $schemaViolation;
        return $this;
    }
}