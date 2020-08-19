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
     * @var integer
     * @OA\Property(
     *     type="integer",
     * )
     */
    protected $id;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     *     description="The image url",
     * )
     */
    protected $url;

    /**
     * @var integer
     * @OA\Property(
     *     type="integer",
     * )
     */
    protected $position;


    /**
     * @var boolean
     * @OA\Property(
     *     type="boolean",
     * )
     */
    protected $visible;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $title;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $description;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $chapo;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $postscriptum;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Image
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return Image
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return Image
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Image
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * @param string $chapo
     *
     * @return Image
     */
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostscriptum()
    {
        return $this->postscriptum;
    }

    /**
     * @param string $postscriptum
     *
     * @return Image
     */
    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }


}