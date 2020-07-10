<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\ProductSaleElements;

/**
 * Class ProductSaleElement
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A product sale element"
 * )
 */
class ProductSaleElement extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer",
     * )
     */
    protected $id;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $reference;

    /**
     * @OA\Property(
     *    description="List of the attributes used by this pse",
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Attribute"
     *     )
     * )
     */
    protected $attributes;

    /**
     * Create an OpenApi ProductSaleElement from a Thelia ProductSaleElements, then returns it
     *
     * @param ProductSaleElements $pse
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaPse(ProductSaleElements $pse)
    {
        $attributes = [];
        foreach ($pse->getAttributeCombinations() as $attributeCombination) {
            $attributes[] = (new Attribute())->createFromTheliaAttributeCombination($attributeCombination);
        }

        $this->id = $pse->getId();
        $this->reference = $pse->getRef();
        $this->attributes = $attributes;

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
     * @return ProductSaleElement
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     * @return ProductSaleElement
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     * @return ProductSaleElement
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
}