<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
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
     * @var int
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
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
     *
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
     *
     * @return CivilityTitle
     */
    public function setLong($long)
    {
        $this->long = $long;

        return $this;
    }

    /**
     * @return CustomerTitle
     */
    protected function getCustomerTitle()
    {
        return new CustomerTitle();
    }
}
