<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Controller\Front\ImageController;
use OpenApi\Service\ImageService;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\BrandImage;
use Thelia\Model\CategoryImage;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ContentImage;
use Thelia\Model\FolderImage;
use Thelia\Model\ModuleImage;
use Thelia\Model\ProductImage;

/**
 * Class Image
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An image for a product, brand, content, module, folder, category or PSE"
 * )
 */
class Image extends BaseApiModel
{
    /**
     * @OA\Property(
     *     type="string",
     *     description="The image url",
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
     * @param ProductImage|ContentImage|BrandImage|CategoryImage|FolderImage|ModuleImage $image
     * @param $imageType
     * @param ImageService $imageService
     * @return $this
     */
    public function createFromTheliaImage($image, $imageType, ImageService $imageService)
    {
        $this->title = $image->getTitle();
        $this->url = $imageService->getImageUrl($image, $imageType);

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
     * @return Image
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
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}