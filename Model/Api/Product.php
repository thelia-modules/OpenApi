<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Controller\Front\ImageController;
use OpenApi\Service\ImageService;
use Thelia\Model\Country;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElements;

/**
 * Class Product
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A product"
 * )
 */
class Product extends BaseApiModel
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
     *     type="string",
     * )
     */
    protected $url;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Image"
     *     )
     * )
     */
    protected $images;

    /**
     * Create an OpenApi Product from a Thelia CartItem and a Country
     *
     * @param \Thelia\Model\CartItem $cartItem
     * @param Country $country
     * @param ImageService $imageService
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaCartItemAndCountry(\Thelia\Model\CartItem $cartItem, Country $country, ImageService $imageService)
    {
        $product = $cartItem->getProduct();
        $images = [];

        foreach ($product->getProductImages() as $productImage) {
            $images[] = (new Image())->createFromTheliaImage($productImage, 'product', $imageService);
        }

        $this->id = $cartItem->getProductId();
        $this->reference = $product->getRef();
        $this->url = $product->getUrl();
        $this->title = $product->getTitle();
        $this->images = $images;

        return $this;
    }

    /**
     * Create an OpenApi Product from a Thelia Pse and a Country
     *
     * @param ProductSaleElements $pse
     * @param Country $country
     * @param ImageService $imageService
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaPseAndCountry(ProductSaleElements $pse, Country $country, ImageService $imageService)
    {
        $product = $pse->getProduct();
        $images = [];

        foreach ($product->getProductImages() as $productImage) {
            $images[] = (new Image())->createFromTheliaImage($productImage, 'product', $imageService);
        }

        $this->id = $product->getId();
        $this->reference = $product->getRef();
        $this->url = $product->getUrl();
        $this->title = $product->getTitle();
        $this->images = $images;

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
     * @return Product
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
     * @return Product
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return Product
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return Product
     */
    public function setImage($images)
    {
        $this->images = $images;
        return $this;
    }
}