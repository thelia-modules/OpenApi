<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CivilityTitle",
 *     title="CivilityTitle",
 *     description="Civility Title model"
 * )
 */
class CivilityTitle extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer"
     * )
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $short;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $long;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return CivilityTitle
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param mixed $short
     *
     * @return CivilityTitle
     */
    public function setShort($short)
    {
        $this->short = $short;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * @param mixed $long
     *
     * @return CivilityTitle
     */
    public function setLong($long)
    {
        $this->long = $long;
        return $this;
    }
}