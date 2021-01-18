<?php

namespace OpenApi\Model\Api;

use OpenApi\Model\Api\ModelTrait\translatable;
use OpenApi\Annotations as OA;

/**
 * Class Content
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A Content"
 * )
 */
class Content extends BaseApiModel
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
     * @return Content
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
     * @return Content
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }
}