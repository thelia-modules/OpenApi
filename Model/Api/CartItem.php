<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use OpenApi\Service\ImageService;
use Thelia\Model\CartItem as TheliaCartItem;
use Thelia\Model\Country;

/**
 * Class CartItem.
 *
 * @OA\Schema(
 *     description="An item in a cart"
 * )
 */
class CartItem extends BaseApiModel
{
    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     *    description="cartItemId, not to be confused with the productId or pseId",
     * )
     * @Constraint\NotBlank(groups={"read, update"})
     */
    protected $id;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $isPromo;

    /**
     * @var Product
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Product",
     * )
     */
    protected $product;

    /**
     * @var ProductSaleElement
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/ProductSaleElement",
     * )
     */
    protected $productSaleElement;

    /**
     * @var array
     * @OA\Property(
     *    description="The pse images if they're present, the product images otherwise",
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/File"
     *     )
     * )
     */
    protected $images;

    /**
     * @var Price
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Price",
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
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     */
    protected $quantity;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedTotalPrice;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedTotalPromoPrice;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedTotalTaxedPrice;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedTotalPromoTaxedPrice;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedRealPrice;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedRealTaxedPrice;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedRealTotalPrice;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $calculatedRealTotalTaxedPrice;
    /**
     * Create a new OpenApi CartItem from a Thelia CartItem and a Country, then returns it.
     *
     * @param \Thelia\Model\CartItem $cartItem
     * @param ImageService           $imageService
     *
     * @return $this
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function fillFromTheliaCartItemAndCountry(TheliaCartItem $cartItem, Country $country)
    {
        $this->id = $cartItem->getId();
        /** @var Product $product */
        $product = $this->modelFactory->buildModel('Product', $cartItem->getProduct());
        $this->product = $product;

        /** @var ProductSaleElement $productSaleElements */
        $productSaleElements = $this->modelFactory->buildModel('ProductSaleElement');
        $productSaleElements->fillFromTheliaPseAndCountry($cartItem->getProductSaleElements(), $country);
        $this->productSaleElement = $productSaleElements;

        $this->isPromo = (bool) $cartItem->getPromo();
        $this->price = $this->modelFactory->buildModel(
            'Price',
            [
                'taxed' => $cartItem->getTaxedPrice($country),
                'untaxed' => $cartItem->getPrice(),
            ]
        );
        $this->promoPrice = $this->modelFactory->buildModel(
            'Price',
            [
                'taxed' => $cartItem->getTaxedPromoPrice($country),
                'untaxed' => $cartItem->getPromoPrice(),
            ]
        );
        $this->quantity = $cartItem->getQuantity();
        //reproduce cart loop comportement
        $this->calculatedTotalPrice = $cartItem->getTotalPrice();
        $this->calculatedTotalPromoPrice = $cartItem->getTotalPromoPrice();
        $this->calculatedTotalTaxedPrice = $cartItem->getTotalTaxedPrice($country);
        $this->calculatedTotalPromoTaxedPrice = $cartItem->getTotalTaxedPromoPrice($country);

        $this->calculatedRealPrice = $cartItem->getRealPrice();
        $this->calculatedRealTaxedPrice = $cartItem->getRealTaxedPrice($country);
        $this->calculatedRealTotalPrice = $cartItem->getTotalRealPrice();
        $this->calculatedRealTotalTaxedPrice = $cartItem->getTotalTaxedPrice($country);

        /** If there are PSE specific images, we use them. Otherwise, we just use the product images */
        $modelFactory = $this->modelFactory;

        try {
            $images = array_map(
                fn ($productSaleElementsImage) => $modelFactory->buildModel('Image', $productSaleElementsImage->getProductImage()),
                iterator_to_array($cartItem->getProductSaleElements()->getProductSaleElementsProductImages())
            );
        } catch (\Exception $exception) {
            $images = [];
        }

        $this->images = !empty($images) ? $images : $this->product->getImages();

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
     * @return CartItem
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
     * @return CartItem
     */
    public function setIsPromo($isPromo)
    {
        $this->isPromo = $isPromo;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return CartItem
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return ProductSaleElement
     */
    public function getProductSaleElement()
    {
        return $this->productSaleElement;
    }

    /**
     * @param ProductSaleElement $productSaleElement
     *
     * @return CartItem
     */
    public function setProductSaleElement($productSaleElement)
    {
        $this->productSaleElement = $productSaleElement;

        return $this;
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
     * @return CartItem
     */
    public function setImages($images)
    {
        $this->images = $images;

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
     * @return CartItem
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
     * @return CartItem
     */
    public function setPromoPrice($promoPrice)
    {
        $this->promoPrice = $promoPrice;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCalculatedRealPrice(): float
    {
        return $this->calculatedRealPrice;
    }

    public function setCalculatedRealPrice(float $calculatedRealPrice): CartItem
    {
        $this->calculatedRealPrice = $calculatedRealPrice;
        return $this;
    }

    public function getCalculatedRealTaxedPrice(): float
    {
        return $this->calculatedRealTaxedPrice;
    }

    public function setCalculatedRealTaxedPrice(float $calculatedRealTaxedPrice): CartItem
    {
        $this->calculatedRealTaxedPrice = $calculatedRealTaxedPrice;
        return $this;
    }

    public function getCalculatedRealTotalPrice(): float
    {
        return $this->calculatedRealTotalPrice;
    }

    public function setCalculatedRealTotalPrice(float $calculatedRealTotalPrice): CartItem
    {
        $this->calculatedRealTotalPrice = $calculatedRealTotalPrice;
        return $this;
    }

    public function getCalculatedRealTotalTaxedPrice(): float
    {
        return $this->calculatedRealTotalTaxedPrice;
    }

    public function setCalculatedRealTotalTaxedPrice(float $calculatedRealTotalTaxedPrice): CartItem
    {
        $this->calculatedRealTotalTaxedPrice = $calculatedRealTotalTaxedPrice;
        return $this;
    }

    public function getCalculatedTotalPrice(): float
    {
        return $this->calculatedTotalPrice;
    }

    public function setCalculatedTotalPrice(float $calculatedTotalPrice): CartItem
    {
        $this->calculatedTotalPrice = $calculatedTotalPrice;
        return $this;
    }

    public function getCalculatedTotalPromoPrice(): float
    {
        return $this->calculatedTotalPromoPrice;
    }

    public function setCalculatedTotalPromoPrice(float $calculatedTotalPromoPrice): CartItem
    {
        $this->calculatedTotalPromoPrice = $calculatedTotalPromoPrice;
        return $this;
    }

    public function getCalculatedTotalPromoTaxedPrice(): float
    {
        return $this->calculatedTotalPromoTaxedPrice;
    }

    public function setCalculatedTotalPromoTaxedPrice(float $calculatedTotalPromoTaxedPrice): CartItem
    {
        $this->calculatedTotalPromoTaxedPrice = $calculatedTotalPromoTaxedPrice;
        return $this;
    }

    public function getCalculatedTotalTaxedPrice(): float
    {
        return $this->calculatedTotalTaxedPrice;
    }

    public function setCalculatedTotalTaxedPrice(float $calculatedTotalTaxedPrice): CartItem
    {
        $this->calculatedTotalTaxedPrice = $calculatedTotalTaxedPrice;
        return $this;
    }
}
