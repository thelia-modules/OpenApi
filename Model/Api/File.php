<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class File
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A file for a product, brand, content, module, folder, category or PSE"
 * )
 */
class File extends BaseApiModel
{
    use translatable;

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
     *     description="The file url",
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return File
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
     * @return File
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
     * @return File
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
     * @return File
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }
}