<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\Country;
use Thelia\Model\ProductPriceQuery;
use Thelia\Model\ProductSaleElements;

/**
 * Class Price
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A price"
 * )
 */
class Price extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $isPromo;

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
    protected $taxed;

    /**
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Promo",
     *    description="A promo object containing untaxed and taxed promo prices",
     * )
     */
    protected $promo;

    /**
     * Create a new OpenApi Price from a Thelia ProductSaleElements and a Country, then returns it
     *
     * @param ProductSaleElements $pse
     * @param Country $country
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaPseAndCountry(ProductSaleElements $pse, Country $country)
    {
        $price = ProductPriceQuery::create()->filterByProductSaleElements($pse)->findOne();
        $pse->setVirtualColumn('price_PRICE', (float)$price->getPrice());
        $pse->setVirtualColumn('price_PROMO_PRICE', (float)$price->getPromoPrice());
        $this->isPromo = $pse->getPromo();
        $this->untaxed = $pse->getPrice();
        $this->taxed = $pse->getTaxedPrice($country);
        $this->promo = (new Promo())->createFromTheliaPseAndCountry($pse, $country);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPromo()
    {
        return $this->isPromo;
    }

    /**
     * @param mixed $isPromo
     * @return Price
     */
    public function setIsPromo($isPromo)
    {
        $this->isPromo = $isPromo;
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
     * @return Price
     */
    public function setUntaxed($untaxed)
    {
        $this->untaxed = $untaxed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaxed()
    {
        return $this->taxed;
    }

    /**
     * @param mixed $taxed
     * @return Price
     */
    public function setTaxed($taxed)
    {
        $this->taxed = $taxed;
        return $this;
    }

    /**
     * @return Promo
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * @param Promo $promo
     * @return Price
     */
    public function setPromo(Promo $promo)
    {
        $this->promo = $promo;
        return $this;
    }


}