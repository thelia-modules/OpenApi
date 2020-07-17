<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\AttributeAv;
use Thelia\Model\AttributeCombination;
use OpenApi\Constraint as Constraint;

/**
 * Class Attribute
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An attribute for a PSE"
 * )
 */
class Attribute extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *         @OA\Property(
     *             type="string",
     *         )
     *     )
     * )
     */
    protected $value;

    /**
     * Create an OpenApi attribute from a Thelia AttributeCombination, then returns it
     *
     * @param AttributeCombination $attributeCombination
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaAttributeCombination(AttributeCombination $attributeCombination)
    {
        $values = [];
        /** @var AttributeAv $attributeValue */
        foreach ($attributeCombination->getAttributeAv() as $attributeValue) {
            $values[] = $attributeValue->getTitle();
        }

        $this->id = $attributeCombination->getAttributeId();
        $this->title = $attributeCombination->getAttribute()->getTitle();
        $this->value = $values;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Attribute
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Attribute
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Attribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


}