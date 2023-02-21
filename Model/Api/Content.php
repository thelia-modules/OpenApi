<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelTrait\hasImages;
use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class Content.
 *
 * @OA\Schema(
 *     description="A Content"
 * )
 */
class Content extends BaseApiModel
{
    use translatable;
    use hasImages;

    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var bool
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
