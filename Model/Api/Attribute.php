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
     * @var integer
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $title;

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
    protected $value;

    /**
     * Create an OpenApi attribute from a Thelia AttributeCombination, then returns it
     *
     * @param AttributeCombination $attributeCombination
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaModel($theliaModel, $locale = null)
    {
        parent::createFromTheliaModel($theliaModel, $locale = null);

        $this->value = array_map(
            function (AttributeAv $attributeValue) {
                return $attributeValue->getTitle();
            },
            $theliaModel->getAttributeAv());

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Attribute
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Attribute
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array $value
     * @return Attribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}