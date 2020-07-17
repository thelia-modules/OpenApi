<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Thelia\Model\CustomerTitle;
use OpenApi\Constraint as Constraint;

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
     * @var integer
     * @OA\Property(
     *    type="integer"
     * ),
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * ),
     */
    protected $short;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CivilityTitle
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param string $short
     * @return CivilityTitle
     */
    public function setShort($short)
    {
        $this->short = $short;
        return $this;
    }

    /**
     * @return string
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * @param string $long
     * @return CivilityTitle
     */
    public function setLong($long)
    {
        $this->long = $long;
        return $this;
    }
}