<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelTrait\translatable;
use OpenApi\Service\ImageService;
use Propel\Runtime\Collection\Collection;
use Thelia\Model\Base\ProductCategory;
use Thelia\Model\Country;
use Thelia\Model\FeatureProduct;
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
     * @var array
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Category"
     *     )
     * )
     */
    protected $categories;

    /**
     * @var array
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Content"
     *     )
     * )
     */
    protected $contents = [];

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/File"
     *     )
     * )
     */
    protected $images = [];

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/File"
     *     )
     * )
     */
    protected $documents = [];

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Feature"
     *     )
     * )
     */
    protected $features = [];

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/ProductSaleElement"
     *     )
     * )
     */
    protected $productSaleElements = [];

    /**
     * @param \Thelia\Model\Product $theliaModel
     * @param null $locale
     *
     * @return Product|void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaModel($theliaModel, $locale = null)
    {
        parent::createFromTheliaModel($theliaModel, $locale);

        $modelFactory = $this->modelFactory;
        $this->features = array_filter(
            array_map(
                function (FeatureProduct $featureProduct) use ($modelFactory){
                    $propelFeature = $featureProduct->getFeature();
                    if(null === $propelFeature){
                        return false;
                    }
                    if (null !== $featureProduct->getFeatureAv()) {
                        // Temporary set only product feature av to build good feature av list
                        $propelFeature->addFeatureAv($featureProduct->getFeatureAv());
                    }
                    $feature = $modelFactory->buildModel('Feature', $propelFeature);

                    $propelFeature->clearFeatureAvs();
                    return $feature;
                },
                iterator_to_array($theliaModel->getFeatureProducts())
            ),
            function($value){
                return $value;
            }
        );

        $this->categories = array_map(
            function (ProductCategory  $productCategory) use ($modelFactory){
                $propelCategory = $productCategory->getCategory();

                $category = $modelFactory->buildModel('Category', $propelCategory);

                if ($productCategory->isDefaultCategory()) {
                    $this->defaultCategory = $category;
                }

                return $category;
            },
            iterator_to_array($theliaModel->getProductCategories())
        );

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
     * Method alias to match thelia getter name
     * @param string $reference
     *
     * @return Product
     */
    public function setRef($reference)
    {
        $this->setReference($reference);
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

    /**
     * @return array
     */
    public function getProductSaleElements()
    {
        return $this->productSaleElements;
    }

    /**
     * @param array $productSaleElements
     *
     * @return Product
     */
    public function setProductSaleElements($productSaleElements)
    {
        $this->productSaleElements = $productSaleElements;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVirtual(): bool
    {
        return $this->virtual;
    }

    /**
     * @param bool $virtual
     * @return Product
     */
    public function setVirtual(bool $virtual): Product
    {
        $this->virtual = $virtual;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     * @return Product
     */
    public function setVisible(bool $visible): Product
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return Brand
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     * @return Product
     */
    public function setBrand(Brand $brand = null): Product
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return Category
     */
    public function getDefaultCategory(): Category
    {
        return $this->defaultCategory;
    }

    /**
     * @param Category $defaultCategory
     * @return Product
     */
    public function setDefaultCategory(Category $defaultCategory): Product
    {
        $this->defaultCategory = $defaultCategory;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return Product
     */
    public function setCategories(array $categories): Product
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Method alias to match thelia getter name
     * @param array $categories
     *
     * @return Product
     */
    public function setProductCategories(array $categories = []): Product
    {
        return $this->setCategories($categories);
    }

    /**
     * @return array
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * @param array $contents
     * @return Product
     */
    public function setContents(array $contents = []): Product
    {
        $this->contents = $contents;
        return $this;
    }

    /**
     * @return array
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @param array $documents
     * @return Product
     */
    public function setDocuments(array $documents = []): Product
    {
        $this->documents = $documents;
        return $this;
    }

    /**
     * @return array
     */
    public function getFeatures(): array
    {
        return $this->features;
    }
}
