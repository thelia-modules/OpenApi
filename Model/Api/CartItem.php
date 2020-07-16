<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Service\ImageService;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Country;
use Thelia\Model\ProductSaleElementsQuery;
use \Thelia\Model\CartItem as TheliaCartItem;

/**
 * Class CartItem
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An item in a cart"
 * )
 */
class CartItem extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer",
     *    description="cartItemId, not to be confused with the productId or pseId",
     * )
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
     *    type="object",
     *    ref="#/components/schemas/Product",
     * )
     */
    protected $product;

    /**
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/ProductSaleElement",
     * )
     */
    protected $productSaleElement;

    /**
     * @OA\Property(
     *    description="The pse images if they're present, the product images otherwise",
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Image"
     *     )
     * )
     */
    protected $images;

    /**
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Price",
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
     * @OA\Property(
     *    type="integer",
     * )
     */
    protected $quantity;

    /**
     * Create a new OpenApi CartItem from a Thelia CartItem and a Country, then returns it
     *
     * @param \Thelia\Model\CartItem $cartItem
     * @param Country $country
     * @param ImageService $imageService
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaCartItemAndCountry(TheliaCartItem $cartItem, Country $country, ImageService $imageService)
    {
        $pse = $cartItem->getProductSaleElements();

        $this->id = $cartItem->getId();
        $this->product = (new Product())->createFromTheliaCartItemAndCountry($cartItem, $country, $imageService);
        $this->productSaleElement = (new ProductSaleElement())->createFromTheliaPseAndCountry($pse, $country);
        $this->isPromo = (bool)$cartItem->getPromo();
        $this->price = (new Price())->setTaxed($cartItem->getPrice());
        $this->promoPrice = (new Price())->setTaxed($cartItem->getPromoPrice());
        $this->quantity = $cartItem->getQuantity();

        /** If there are PSE specific images, we use them. Otherwise, we just use the product images */
        $images = [];
        $pseImages = $pse->getProductSaleElementsProductImages();
        if (!empty($pseImages->getData())) {
            foreach ($pseImages as $pseImage) {
                $images[] = (new Image())->createFromTheliaImage($pseImage->getProductImage(), 'product', $imageService);
            }
        }

        $this->images = !empty($images) ? $images : $this->product->getImages();

        return $this;
    }

    public function createFromJsonAndCountry($json, Country $country, ImageService $imageService)
    {
        $this->createFromJson($json);

        $cartItem = json_decode($json, true);
        if (!isset($cartItem['pseId'])) {
            throw new \Exception(Translator::getInstance()->trans('A PSE is needed in the POST request to add an item to the cart.'));
        }
        if (!isset($cartItem['quantity'])) {
            throw new \Exception(Translator::getInstance()->trans('A PSE is needed in the POST request to add an item to the cart.'));
        }

        $pse = ProductSaleElementsQuery::create()->findPk($cartItem['pseId']);

        $this->product = (new Product())->createFromTheliaPseAndCountry($pse, $country, $imageService);
        $this->productSaleElement = (new ProductSaleElement())->createFromTheliaPseAndCountry($pse, $country);
        $this->isPromo = $cartItem['isPromo'];
        $this->price = (new Price())->setTaxed($cartItem['price']);
        $this->promoPrice = (new Price())->setTaxed($cartItem['promoPrice']);
        $this->quantity = $cartItem['quantity'];

        /** If there are PSE specific images, we use them. Otherwise, we just use the product images */
        $images = [];
        $pseImages = $pse->getProductSaleElementsProductImages();
        if (!empty($pseImages->getData())) {
            foreach ($pseImages as $pseImage) {
                $images[] = (new Image())->createFromTheliaImage($pseImage->getProductImage(), 'product', $imageService);
            }
        }

        $this->images = !empty($images) ? $images : $this->product->getImages();

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
     * @return CartItem
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     * @return CartItem
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductSaleElement()
    {
        return $this->productSaleElement;
    }

    /**
     * @param mixed $productSaleElement
     * @return CartItem
     */
    public function setProductSaleElement($productSaleElement)
    {
        $this->productSaleElement = $productSaleElement;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     * @return CartItem
     */
    public function setImages($images)
    {
        $this->images = $images;
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
     * @return CartItem
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
     * @return CartItem
     */
    public function setIsPromo($isPromo)
    {
        $this->isPromo = $isPromo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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
     * @return CartItem
     */
    public function setPromoPrice($promoPrice)
    {
        $this->promoPrice = $promoPrice;
        return $this;
    }
}