<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelTrait\translatable;
use OpenApi\Service\ImageService;
use Thelia\Model\Country;
use Thelia\Model\ProductSaleElements;
use OpenApi\Constraint as Constraint;

/**
 * Class Product
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A product"
 * )
 */
class Product extends BaseApiModel
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
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $reference;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $url;

    /**
     * @var boolean
     * @OA\Property(
     *     type="boolean",
     * )
     */
    protected $virtual;

    /**
     * @var boolean
     * @OA\Property(
     *     type="boolean",
     * )
     */
    protected $visible;

    /**
     * @var Brand
     * @OA\Property(
     *     ref="#/components/schemas/Brand"
     * )
     */
    protected $brand;

    /**
     * @var Category
     * @OA\Property(
     *     ref="#/components/schemas/Category"
     * )
     */
    protected $defaultCategory;

    /**
     * @var Category
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Category"
     *     )
     * )
     */
    protected $categories;

    /**
     * @var Content
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Content"
     *     )
     * )
     */
    protected $contents;

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
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Feature"
     *     )
     * )
     */
    protected $features;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/ProductSaleElement"
     *     )
     * )
     */
    protected $productSaleElements;

    /**
     * Create an OpenApi Product from a Thelia CartItem and a Country
     *
     * @param \Thelia\Model\CartItem $cartItem
     * @param Country $country
     * @param ImageService $imageService
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function fillFromTheliaCartItemAndCountry(\Thelia\Model\CartItem $cartItem)
    {
        $product = $cartItem->getProduct();

        $modelFactory = $this->modelFactory;

        try {
            $images = array_map(
                function ($productImage) use ($modelFactory) {
                    return $modelFactory->buildModel('Image', $productImage);
                },
                iterator_to_array($product->getProductImages())
            );
        } catch (\Exception $e) {
            $images = [];
        }

        $this->id = $cartItem->getProductId();
        $this->reference = $product->getRef();
        $this->url = $product->getUrl();

        $i18n = $modelFactory
            ->buildModel('I18n', $product);
        $this->i18n = $i18n;
        $this->images = $images;

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
     * @return Product
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Product
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Product
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @return Product
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }
}