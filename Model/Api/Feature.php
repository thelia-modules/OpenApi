<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelTrait\translatable;
use OpenApi\Constraint as Constraint;

/**
 * Class Feature
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A feature"
 * )
 */
class Feature extends BaseApiModel
{
    use translatable;

    /**
     * @var integer
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
     * @return Feature
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }
}