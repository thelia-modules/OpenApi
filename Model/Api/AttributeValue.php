<?php

namespace OpenApi\Model\Api;

use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class AttributeValue.
 *
 * @OA\Schema(
 *     description="An attribute value"
 * )
 */
class AttributeValue extends BaseApiModel
{
    static $serviceAliases = ["AttributeAv"];

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AttributeValue
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * "setAttributeAvId" alias to fit Thelia model.
     *
     * @param int $id
     *
     * @return AttributeValue
     */
    public function setAttributeAvId($id)
    {
        return $this->setId($id);
    }
}
