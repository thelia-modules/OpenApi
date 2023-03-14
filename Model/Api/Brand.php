<?php

namespace OpenApi\Model\Api;

use OpenApi\Model\Api\ModelTrait\hasImages;
use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class Brand
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A Brand"
 * )
 */
class Brand extends BaseApiModel
{
    use translatable;
    use hasImages;

    /**
     * @var integer
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var boolean
     * @OA\Property(
     *     type="boolean",
     * )
     */
    protected $visible;

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
     * @return Brand
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Brand
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    public function createFromTheliaModel($theliaModel, $locale = null)
    {
        /** @var \Thelia\Model\Brand $theliaModel */
        $brandImages = $theliaModel->getBrandImagesRelatedByBrandId();

        $images = array_filter(array_map(function ($value) {
            return $this->modelFactory->buildModel('Images', $value);
        }, iterator_to_array($brandImages)));

        $this->setImages($images);

        parent::createFromTheliaModel($theliaModel, $locale);
    }
}