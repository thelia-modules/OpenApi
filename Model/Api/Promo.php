<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\Country;
use Thelia\Model\ProductSaleElements;

/**
 * Class Promo
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A promotion price"
 * )
 */
class Promo extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $untaxed;

    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $promo;

    /**
     * Create a new OpenApi Promo from a Thelia ProductSaleElements and a Country
     *
     * @param ProductSaleElements $pse
     * @param Country $country
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaPseAndCountry(ProductSaleElements $pse, Country $country)
    {
        $this->promo = $pse->getTaxedPromoPrice($country);
        $this->untaxed = $pse->getPromoPrice();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUntaxed()
    {
        return $this->untaxed;
    }

    /**
     * @param mixed $untaxed
     * @return Promo
     */
    public function setUntaxed($untaxed)
    {
        $this->untaxed = $untaxed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * @param mixed $promo
     * @return Promo
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;
        return $this;
    }


}