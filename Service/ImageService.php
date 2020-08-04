<?php


namespace OpenApi\Service;


use OpenApi\Model\Api\Image;
use Symfony\Component\EventDispatcher\Event;
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

class ImageService
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    protected function dispatch($eventName, Event $event = null)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }

    /**
     * Returns an image URL
     *
     * @param $imageModel
     * @param $imageType
     * @return string
     */
    public function getImageUrl($imageModel, $imageType = null)
    {
        return $this->transformImage($imageModel, $imageType);
    }

    /**
     * Transform an image according to the parameters, and returns the
     * transformed image URL
     *
     * imageFile can be of type Thelia\Model\ProductImage, ContentImage etc
     *
     * @param ProductImage|ContentImage|BrandImage|CategoryImage|FolderImage|ModuleImage $imageModel
     * @param $imageType
     * @param bool $allowZoom
     * @param string $resize
     * @param null $width
     * @param null $height
     * @param null $rotation
     * @param null $backgroundColor
     * @param null $quality
     * @param null $effects
     * @return string The transformed Image URL
     */
    public function transformImage(
        $imageModel,
        $imageType = null,
        $allowZoom = false,
        $resize = 'none',
        $width = null,
        $height = null,
        $rotation = null,
        $backgroundColor = null,
        $quality = null,
        $effects = null
    )
    {
        switch ($resize) {
            case 'crop':
                $resizeMode = \Thelia\Action\Image::EXACT_RATIO_WITH_CROP;
                break;

            case 'borders':
                $resizeMode = \Thelia\Action\Image::EXACT_RATIO_WITH_BORDERS;
                break;

            case 'none':
            default:
                $resizeMode = \Thelia\Action\Image::KEEP_IMAGE_RATIO;
        }

        $event = $this->createImageEvent($imageModel, $imageType);
        $event
            ->setAllowZoom($allowZoom)
            ->setResizeMode($resizeMode)
            ->setWidth($width)
            ->setHeight($height)
            ->setRotation($rotation)
            ->setBackgroundColor($backgroundColor)
            ->setQuality($quality)
        ;

        /** Needed as setting effects as null will throw an exception during dispatch */
        if ($effects) {
            $event->setEffects($effects);
        }

        $this->dispatch(TheliaEvents::IMAGE_PROCESS, $event);

        return $event->getFileUrl();
    }

    /**
     * @param ProductImage|ContentImage|BrandImage|CategoryImage|FolderImage|ModuleImage $imageModel
     * @param null $imageType
     * @return ImageEvent
     */
    protected function createImageEvent($imageModel, $imageType = null)
    {
        $imageEvent = new ImageEvent();
        $baseSourceFilePath = ConfigQuery::read('images_library_path');

        if (null === $imageType) {
            $imageType = str_replace(array('image', 'thelia\\model\\'), '', strtolower(get_class($imageModel)));
        }

        if ($baseSourceFilePath === null) {
            $baseSourceFilePath = THELIA_LOCAL_DIR . 'media' . DS . 'images';
        } else {
            $baseSourceFilePath = THELIA_ROOT . $baseSourceFilePath;
        }

        /** Put source image file path */
        $sourceFilePath = sprintf(
            '%s/%s/%s',
            $baseSourceFilePath,
            $imageType,
            $imageModel->getFile()
        );

        $imageEvent->setSourceFilepath($sourceFilePath);
        $imageEvent->setCacheSubdirectory($imageType);

        return $imageEvent;
    }
}