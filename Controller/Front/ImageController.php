<?php


namespace OpenApi\Controller\Front;


use OpenApi\Service\ImageService;
use Thelia\Core\Thelia;

class ImageController extends BaseFrontOpenApiController
{
    /** @var ImageService $imageService */
    protected $imageService;

    public function __construct()
    {
        parent::__construct();

        $this->imageService = $this->getContainer()->get('open_api.image.service');
    }

    /**
     * Returns an image URL
     *
     * @param $imageFile
     * @param $imageType
     * @return string
     */
    public function getImageUrl($imageFile, $imageType)
    {
        return $this->imageService->transformImage($imageFile, $imageType);
    }

    /**
     * Transform an image according to parameters then returns
     * its URL
     *
     * @param $imageFile
     * @param $imageType
     * @param $allowZoom
     * @param $resize
     * @param $width
     * @param $height
     * @param $rotation
     * @param $backgroundColor
     * @param $quality
     * @param $effects
     * @return string
     */
    public function transformImage(
        $imageFile,
        $imageType,
        $allowZoom,
        $resize,
        $width,
        $height,
        $rotation,
        $backgroundColor,
        $quality,
        $effects
    )
    {
        return $this->imageService->transformImage(
            $imageFile,
            $imageType,
            $allowZoom,
            $resize,
            $width,
            $height,
            $rotation,
            $backgroundColor,
            $quality,
            $effects
        );
    }
}