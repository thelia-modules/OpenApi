<?php

namespace OpenApi\Model\Api;

use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\Country;
use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use Thelia\Model\CouponQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\State;
use Thelia\Module\BaseModule;
use Thelia\Module\Exception\DeliveryException;
use Thelia\TaxEngine\TaxEngine;

/**
 * Class Cart
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A cart"
 * )
 */
class Cart extends BaseApiModel
{
    /**
     * @var integer
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read", "update"})
     */
    protected $id;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $taxes;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     *    description="The estimated delivery price for this cart",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $delivery;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Coupon"
     *     )
     * )
     */
    protected $coupons;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $discount;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $total;

    /**
     * @var string
     * @OA\Property(
     *     description="Symbol of the currently used currency",
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $currency;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/CartItem"
     *     )
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $items;

    /**
     * @var Container
     */
    protected $container;

    public function __construct(
        ModelFactory $modelFactory,
        RequestStack $requestStack,
        TaxEngine $taxEngine,
        EventDispatcher $dispatcher,
        // Todo find a way to remove container here (only used to get module instance)
        Container $container
    )
    {
        parent::__construct($modelFactory, $requestStack, $taxEngine, $dispatcher);
        $this->container = $container;
    }

    public function createFromTheliaModel($theliaModel, $locale = null)
    {
        parent::createFromTheliaModel($theliaModel, $locale);
        $estimatedPostage = $this->getEstimatedPostageForCountry($theliaModel, $this->country, $this->state);

        $consumedCoupons = $this->request->getSession()->getConsumedCoupons();
        $coupons = $this->createOpenApiCouponsFromCouponsCodes($consumedCoupons);

        $modelFactory = $this->modelFactory;
        $deliveryCountry = $this->country;
        $cartItems = array_map(
            function ($theliaCartItem) use ($modelFactory, $deliveryCountry) {
                /** @var CartItem $cartItem */
                $cartItem = $modelFactory->buildModel('CartItem', $theliaCartItem);
                $cartItem->fillFromTheliaCartItemAndCountry($theliaCartItem, $deliveryCountry);
                return $cartItem;
            },
            iterator_to_array($theliaModel->getCartItems())
        );

        $this
            ->setTaxes($theliaModel->getTotalVAT($deliveryCountry, null, false))
            ->setDelivery($estimatedPostage)
            ->setCoupons($coupons)
            ->setTotal($theliaModel->getTaxedAmount($deliveryCountry, false, null))
            ->setCurrency($theliaModel->getCurrency()->getSymbol())
            ->setItems($cartItems);
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
     * @return Cart
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param float $taxes
     * @return Cart
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
        return $this;
    }

    /**
     * @return float
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param float $delivery
     * @return Cart
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return array
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * @param array $coupons
     * @return Cart
     */
    public function setCoupons($coupons)
    {
        $this->coupons = $coupons;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     * @return Cart
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return Cart
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Cart
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Cart
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Creates an array of OpenApi coupons from an array of coupons codes, then returns it
     *
     * @param $couponsCodes
     * @return array
     */
    protected function createOpenApiCouponsFromCouponsCodes($couponsCodes)
    {
        $coupons = CouponQuery::create()->filterByCode($couponsCodes)->find();

        $factory = $this->modelFactory;
        return array_map(
            function ($coupon) use ($factory) {
                return $factory->buildModel('Coupon', $coupon);
            },
            iterator_to_array($coupons)
        );
    }

    /**
     * Return the minimum expected postage for a cart in a given country
     *
     * @param \Thelia\Model\Cart $cart
     * @param Country $country
     * @param State|null $state
     * @return float|null
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getEstimatedPostageForCountry(\Thelia\Model\Cart $cart, Country $country, State $state = null)
    {
        $deliveryModules = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE, Criteria::EQUAL)
            ->find()
        ;

        $virtual = $cart->isVirtual();
        $postage = null;

        /** @var \Thelia\Model\Module $deliveryModule */
        foreach ($deliveryModules as $deliveryModule) {
            $areaDeliveryModule = AreaDeliveryModuleQuery::create()
                ->findByCountryAndModule($country, $deliveryModule, $state);

            if (null === $areaDeliveryModule && false === $virtual) {
                continue;
            }

            $moduleInstance = $deliveryModule->getDeliveryModuleInstance($this->container);

            if (true === $virtual && false === $moduleInstance->handleVirtualProductDelivery()) {
                continue;
            }

            try {
                $deliveryPostageEvent = new DeliveryPostageEvent($moduleInstance, $cart, null, $country, $state);
                $this->dispatcher->dispatch(
                    $deliveryPostageEvent,
                    TheliaEvents::MODULE_DELIVERY_GET_POSTAGE
                );

                if ($deliveryPostageEvent->isValidModule()) {
                    $modulePostage = $deliveryPostageEvent->getPostage();

                    if (null === $postage || $postage > $modulePostage->getAmount()) {
                        $postage = $modulePostage->getAmount();
                    }
                }
            } catch (DeliveryException $ex) {
                // Module is not available
            }
        }

        return $postage;
    }
}
