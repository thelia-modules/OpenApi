<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Service\ImageService;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\BrandImage;
use Thelia\Model\CategoryImage;
use Thelia\Model\ContentImage;
use Thelia\Model\FolderImage;
use Thelia\Model\ModuleImage;
use Thelia\Model\ProductImage;
use OpenApi\Constraint as Constraint;

/**
 * Class Image
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An image for a product, brand, content, module, folder, category or PSE"
 * )
 */
class Image extends BaseApiModel
{
    /** @var ImageService */
    protected $imageService;

    public function __construct(ModelFactory $modelFactory, RequestStack $requestStack, ImageService $imageService)
    {
        parent::__construct($modelFactory, $requestStack);
        $this->imageService = $imageService;
    }

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     *     description="The image url",
     * )
     */
    protected $url;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $title;

    /**
     * @param $theliaModel
     * @param null $locale
     * @param null $type
     * @return $this
     */
    public function createFromTheliaModel($theliaModel, $locale = null, $type = null)
    {
        parent::createFromTheliaModel($theliaModel, $locale);
        $this->url = $this->imageService->getImageUrl($theliaModel, $type);

        return $this;
    }

    /**
     * @return ImageService
     */
    public function getImageService()
    {
        return $this->imageService;
    }

    /**
     * @param ImageService $imageService
     * @return Image
     */
    public function setImageService($imageService)
    {
        $this->imageService = $imageService;
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
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }


}