<?php

namespace OpenApi\Model\Api;

use OpenApi\Model\Api\ModelTrait\translatable;
use OpenApi\Annotations as OA;

/**
 * Class Category
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A Category"
 * )
 */
class Category extends BaseApiModel
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
     * @return Category
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
     * @return Category
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }
}
