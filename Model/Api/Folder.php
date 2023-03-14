<?php

namespace OpenApi\Model\Api;

use OpenApi\Model\Api\ModelTrait\hasImages;
use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class Folder
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A Folder"
 * )
 */
class Folder extends BaseApiModel
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
     * @return Folder
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
     * @return Folder
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }
}