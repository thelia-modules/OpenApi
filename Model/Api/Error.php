<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class Error.
 *
 * @OA\Schema(
 *     description="An error"
 * )
 */
class Error extends BaseApiModel
{
    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $title;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $description;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/SchemaViolation"
     *     )
     * )
     */
    protected $schemaViolations;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Error
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Error
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getSchemaViolations()
    {
        return $this->schemaViolations;
    }

    /**
     * @param array $schemaViolations
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
