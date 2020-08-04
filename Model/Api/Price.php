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
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $untaxed;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $taxed;

    /**
     * Create a new OpenApi Price from a Thelia ProductSaleElements and a Country, then returns it
     *
     * @param ProductSaleElements $pse
     * @param Country $country
     * @param bool $isPromo
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function fillFromTheliaPseAndCountry(ProductSaleElements $pse, Country $country, $isPromo = false)
    {
        $price = ProductPriceQuery::create()->filterByProductSaleElements($pse)->findOne();
        $pse->setVirtualColumn('price_PRICE', (float)$price->getPrice());
        $pse->setVirtualColumn('price_PROMO_PRICE', (float)$price->getPromoPrice());
        $this->untaxed = $isPromo ? $pse->getPromoPrice() : $pse->getPrice();
        $this->taxed = $isPromo ? $pse->getTaxedPromoPrice($country) : $pse->getTaxedPrice($country);

        return $this;
    }

    /**
     * @return float
     */
    public function getUntaxed()
    {
        return $this->untaxed;
    }

    /**
     * @param float $untaxed
     * @return Price
     */
    public function setUntaxed($untaxed)
    {
        $this->untaxed = $untaxed;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxed()
    {
        return $this->taxed;
    }

    /**
     * @param float $taxed
     * @return Price
     */
    public function setTaxed($taxed)
    {
        $this->taxed = $taxed;
        return $this;
    }
}