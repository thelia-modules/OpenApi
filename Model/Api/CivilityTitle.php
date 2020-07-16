<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Thelia\Model\CustomerTitle;

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
     * ),
     * @Assert\NotNull(groups={"read", "update"})
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="string"
     * ),
     * @Assert\NotNull(groups={"create", "update"})
     */
    protected $short;

    /**
     * @OA\Property(
     *    type="string"
     * )
     * @Assert\NotNull(groups={"create", "update"})
     */
    protected $long;

    /**
     * Create an OpenApi CivilityTitle from a Thelia CustomerTitle, then returns it
     *
     * @param CustomerTitle $title
     * @return $this
     */
    public function createFromTheliaCustomerTitle(CustomerTitle $title)
    {
        $this->id = $title->getId();
        $this->short = $title->getShort();
        $this->long = $title->getLong();

        return $this;
    }

    public function createFromArray($array)
    {

    }

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