<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class Feature.
 *
 * @OA\Schema(
 *     description="A feature"
 * )
 */
class Feature extends BaseApiModel
{
    use translatable;

    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var array
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *         @OA\Property(
     *             type="string",
     *         )
     *     )
     * )
     */
    protected $values;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Feature
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     *
     * @return Feature
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * "setValues" alias to fit Thelia model.
     *
     * @param array $values
     *
     * @return Feature
     */
    public function setFeatureAvs($values)
    {
        return $this->setValues($values);
    }
}
