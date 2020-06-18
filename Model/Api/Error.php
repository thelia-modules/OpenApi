<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

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
     *          ref="#/components/schemas/Violations"
     *     )
     * )
     */
    protected $violations;

    /**
     * Error constructor.
     *
     * @param null $title
     * @param null $description
     */
    public function __construct(
        $title = null,
        $description = null
    )
    {
        $this->title = $title;
        $this->description = $description;
    }

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
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param $violations Violation[]
     * @return Error
     */
    public function setViolations($violations)
    {
        $this->violations = $violations;

        return $this;
    }

    /**
     * @param $violation Violation
     *
     * @return $this
     */
    public function appendViolation($violation)
    {
        $this->violations[] = $violation;
        return $this;
    }
}