<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Propel\Runtime\Collection\Collection;
use Thelia\Model\AttributeCombination;
use Thelia\Model\Country;
use Thelia\Model\ProductPriceQuery;
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
     * @var integer
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var boolean
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $isPromo;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $reference;

    /**
     * @var array
     * @OA\Property(
     *    description="List of the attributes and its value used by this pse",
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Attribute"
     *     )
     * )
     */
    protected $attributes;

    /**
     * @var float
     * @OA\Property(
     *     type="number",
     *     format="float"
     * )
     */
    protected $quantity;

    /**
     * @var boolean
     * @OA\Property(
     *     type="boolean"
     * )
     */
    protected $newness;

    /**
     * @var float
     * @OA\Property(
     *     type="number",
     *     format="float"
     * )
     */
    protected $weight;

    /**
     * @var boolean
     * @OA\Property(
     *     type="boolean"
     * )
     */
    protected $isDefault;

    /**
     * @var string
     * @OA\Property(
     *     type="string"
     * )
     */
    protected $ean;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/File"
     *     )
     * )
     */
    protected $images;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/File"
     *     )
     * )
     */
    protected $documents;

    /**
     * @var Price
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Price"
     * )
     */
    protected $price;

    /**
     * @var Price
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Price"
     * )
     */
    protected $promoPrice;

    /**
     * @param ProductSaleElements $theliaModel
     * @param null $locale
     *
     * @return ProductSaleElement|void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaModel($theliaModel, $locale = null)
    {
        $price = ProductPriceQuery::create()->filterByProductSaleElements($theliaModel)->findOne();
        $theliaModel->setVirtualColumn('price_PRICE', (float)$price->getPrice());
        $theliaModel->setVirtualColumn('price_PROMO_PRICE', (float)$price->getPromoPrice());

        parent::createFromTheliaModel($theliaModel, $locale);

        $modelFactory = $this->modelFactory;
        $this->attributes = array_map(
            function (AttributeCombination $attributeCombination) use ($modelFactory){
                $propelAttribute = $attributeCombination->getAttribute();

                // Temporary set only pse attribute av to build good attribute av list
                $propelAttribute->addAttributeAv($attributeCombination->getAttributeAv());

                $attribute = $modelFactory->buildModel('Attribute', $propelAttribute);

                // Reset attribute av to all for next use of attribute (because of propel "cache")
                $propelAttribute->clearAttributeAvs();
                return $attribute;
            },
            iterator_to_array($theliaModel->getAttributeCombinations())
        );

        $this->isPromo = (bool)$theliaModel->getPromo();
        $this->price = $this->modelFactory->buildModel('Price', ['untaxed' => $theliaModel->getPrice(), 'taxed' => $theliaModel->getTaxedPrice($this->country)]);;
        $this->promoPrice = $this->modelFactory->buildModel('Price', ['untaxed' => $theliaModel->getPromoPrice(), 'taxed' => $theliaModel->getTaxedPromoPrice($this->country)]);;
    }

    /**
     * Create an OpenApi ProductSaleElement from a Thelia ProductSaleElements and a Country, then returns it
     *
     * @param ProductSaleElements $pse
     * @param Country $country
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function fillFromTheliaPseAndCountry(ProductSaleElements $pse, Country $country)
    {
        $modelFactory = $this->modelFactory;
        $attributes = array_map(
            function (AttributeCombination $attributeCombination) use ($modelFactory){
                $attribute = $attributeCombination->getAttribute();
                $attribute->setAttributeAvs((new Collection()));
                $attribute->addAttributeAv($attributeCombination->getAttributeAv());
                return $modelFactory->buildModel('Attribute', $attribute);
            },
            iterator_to_array($pse->getAttributeCombinations())
        );

        $this->id = $pse->getId();
        $this->isPromo = (bool)$pse->getPromo();

        /** @var Price $price */
        $price = $this->modelFactory->buildModel('Price');
        $price->fillFromTheliaPseAndCountry($pse, $country);
        $this->price = $price;

        /** @var Price $promoPrice */
        $promoPrice = $this->modelFactory->buildModel('Price');
        $promoPrice->fillFromTheliaPseAndCountry($pse, $country);
        $this->promoPrice = $price;

        $this->reference = $pse->getRef();
        $this->attributes = $attributes;

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
     *
     * @return ProductSaleElement
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPromo()
    {
        return $this->isPromo;
    }

    /**
     * @param bool $isPromo
     *
     * @return ProductSaleElement
     */
    public function setIsPromo($isPromo)
    {
        $this->isPromo = $isPromo;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return ProductSaleElement
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }


    /**
     * @param $reference
     *
     * @return $this
     */
    public function setRef($reference)
    {
        return $this->setReference($reference);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return ProductSaleElement
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     *
     * @return ProductSaleElement
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNewness()
    {
        return $this->newness;
    }

    /**
     * @param bool $newness
     *
     * @return ProductSaleElement
     */
    public function setNewness($newness)
    {
        $this->newness = $newness;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return ProductSaleElement
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     *
     * @return ProductSaleElement
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    /**
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param string $ean
     *
     * @return ProductSaleElement
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
        return $this;
    }

    /**
     * @param string $ean
     *
     * @return ProductSaleElement
     */
    public function setEanCode($ean)
    {
        return $this->setEan($ean);
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return ProductSaleElement
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param array $documents
     *
     * @return ProductSaleElement
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
        return $this;
    }

    /**
     * @return Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param Price $price
     *
     * @return ProductSaleElement
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Price
     */
    public function getPromoPrice()
    {
        return $this->promoPrice;
    }

    /**
     * @param Price $promoPrice
     *
     * @return ProductSaleElement
     */
    public function setPromoPrice($promoPrice)
    {
        $this->promoPrice = $promoPrice;
        return $this;
    }
}
