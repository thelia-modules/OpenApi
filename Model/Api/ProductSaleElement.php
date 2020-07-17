<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\Country;
use Thelia\Model\ProductSaleElements;
use OpenApi\Constraint as Constraint;

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
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $isPromo;

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
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Price"
     * )
     */
    protected $price;

    /**
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Price"
     * )
     */
    protected $promoPrice;

    /**
     * Create an OpenApi ProductSaleElement from a Thelia ProductSaleElements and a Country, then returns it
     *
     * @param ProductSaleElements $pse
     * @param Country $country
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaPseAndCountry(ProductSaleElements $pse, Country $country)
    {
        $attributes = [];
        foreach ($pse->getAttributeCombinations() as $attributeCombination) {
            $attributes[] = (new Attribute())->createFromTheliaAttributeCombination($attributeCombination);
        }

        $this->id = $pse->getId();
        $this->isPromo = (bool)$pse->getPromo();
        $this->price = (new Price())->createFromTheliaPseAndCountry($pse, $country);
        $this->promoPrice = (new Price())->createFromTheliaPseAndCountry($pse, $country, true);
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

    /**
     * @return mixed
     */
    public function getIsPromo()
    {
        return $this->isPromo;
    }

    /**
     * @param mixed $isPromo
     * @return ProductSaleElement
     */
    public function setIsPromo($isPromo)
    {
        $this->isPromo = $isPromo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return ProductSaleElement
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPromoPrice()
    {
        return $this->promoPrice;
    }

    /**
     * @param mixed $promoPrice
     * @return ProductSaleElement
     */
    public function setPromoPrice($promoPrice)
    {
        $this->promoPrice = $promoPrice;
        return $this;
    }
}