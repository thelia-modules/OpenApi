<?php

namespace OpenApi\Model\Api\ModelTrait;


/**
 * @OA\Schema
 * Trait translatable
 */
trait hasImages
{
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
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return $this
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    public function sortImagesByPosition()
    {
        usort($this->images, function ($item1, $item2) {
            return $item1->getPosition() <=> $item2->getPosition();
        });
    }
}
