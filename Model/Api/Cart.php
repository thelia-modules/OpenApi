<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Domain\Promotion\Coupon\CouponManager;
use Thelia\Domain\Promotion\Coupon\Type\CouponInterface;
use Thelia\Model\AddressQuery;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\Country;
use Thelia\Model\CouponCountry;
use Thelia\Model\CouponModule;
use Thelia\Model\CouponQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Model\State;
use Thelia\Module\BaseModule;
use Thelia\Module\Exception\DeliveryException;
use Thelia\Domain\Taxation\TaxEngine\TaxEngine;

/**
 * Class Cart.
 *
 * @OA\Schema(
 *     description="A cart"
 * )
 */
class Cart extends BaseApiModel
{
    /**
     * @var int
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
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     *    description="The estimated delivery tax price for this cart",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $deliveryTax;

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
     * @var boolean
     * @OA\Property(
     *    type="boolean"
     * )
     */
    protected $virtual;

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
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $totalWithoutTax;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var CouponManager
     */
    protected $couponManager;

    public function __construct(
        ModelFactory $modelFactory,
        RequestStack $requestStack,
        TaxEngine $taxEngine,
        EventDispatcherInterface $dispatcher,
        ValidatorInterface $validator,
        // Todo find a way to remove container here (only used to get module instance)
        ContainerInterface $container,
        CouponManager $couponManager
    )
    {
        parent::__construct($modelFactory, $requestStack, $taxEngine, $dispatcher, $validator);
        $this->container = $container;
        $this->couponManager = $couponManager;
    }

    public function createFromTheliaModel($theliaModel, $locale = null): void
    {
        parent::createFromTheliaModel($theliaModel, $locale);
        $postageInfo = $this->getEstimatedPostageForCountry($theliaModel, $this->country, $this->state);
        $estimatedPostage = $postageInfo['postage'];
        $postageTax = $postageInfo['tax'];

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
            ->setTotalWithoutTax($theliaModel->getTotalAmount())
            ->setDeliveryTax($postageTax)
            ->setTaxes($theliaModel->getTotalVAT($deliveryCountry, null, false))
            ->setDelivery($estimatedPostage)
            ->setCoupons($coupons)
            ->setTotal($theliaModel->getTaxedAmount($deliveryCountry, false, null))
            ->setCurrency($theliaModel->getCurrency()->getSymbol())
            ->setItems($cartItems)
            ->setVirtual($theliaModel->isVirtual());
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
     *
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
     *
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
     *
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
     *
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
    public function getDeliveryTax()
    {
        return $this->deliveryTax;
    }

    /**
     * @param float $deliveryTax
     */
    public function setDeliveryTax($deliveryTax)
    {
        $this->deliveryTax = $deliveryTax;

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
     *
     * @return Cart
     */
    public function setDiscount($discount)
    {
        $this->discount = (float)$discount;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getVirtual()
    {
        return $this->virtual;
    }

    /**
     * @param boolean $virtual
     * @return Cart
     */
    public function setVirtual($virtual)
    {
        $this->virtual = $virtual;
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
     *
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
     *
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
     *
     * @return Cart
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    public function getTotalWithoutTax(): float
    {
        return $this->totalWithoutTax;
    }

    public function setTotalWithoutTax(float $totalWithoutTax): Cart
    {
        $this->totalWithoutTax = $totalWithoutTax;
        return $this;
    }

    /**
     * Creates an array of OpenApi coupons from an array of coupons codes, then returns it.
     *
     * @param $couponsCodes
     *
     * @return array
     */
    protected function createOpenApiCouponsFromCouponsCodes($couponsCodes)
    {
        $coupons = CouponQuery::create()->filterByCode($couponsCodes)->find();

        $factory = $this->modelFactory;

        return array_map(
            fn ($coupon) => $factory->buildModel('Coupon', $coupon),
            iterator_to_array($coupons)
        );
    }

    /**
     * Return the minimum expected postage for a cart in a given country.
     *
     * @return array
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getEstimatedPostageForCountry(\Thelia\Model\Cart $cart, Country $country, State $state = null)
    {
        $orderSession = $this->request->getSession()->getOrder();
        $deliveryModules = [];

        if ($deliveryModule = ModuleQuery::create()->findPk($orderSession->getDeliveryModuleId())) {
            $deliveryModules[] = $deliveryModule;
        }

        if (empty($deliveryModules)) {
            $deliveryModules = ModuleQuery::create()
                ->filterByActivate(1)
                ->filterByType(BaseModule::DELIVERY_MODULE_TYPE, Criteria::EQUAL)
                ->find();
        }

        $virtual = $cart->isVirtual();
        $postage = null;
        $postageTax = null;

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
                        $postage = $modulePostage->getAmount() - $modulePostage->getAmountTax();
                        $postageTax = $modulePostage->getAmountTax();
                    }
                }
            } catch (DeliveryException $ex) {
                // Module is not available
            }
        }

        if ($this->isCouponRemovingPostage($country->getId(), $deliveryModule->getId())) {
            $postage = 0;
            $postageTax = 0;
        }

        return [
            'postage' => $postage,
            'tax' => $postageTax
        ];
    }

    private function isCouponRemovingPostage(int $countryId, int $deliveryModuleId)
    {
        $couponsKept = $this->couponManager->getCouponsKept();

        if (\count($couponsKept) == 0) {
            return false;
        }

        /** @var CouponInterface $coupon */
        foreach ($couponsKept as $coupon) {
            if (!$coupon->isRemovingPostage()) {
                continue;
            }

            // Check if delivery country is on the list of countries for which delivery is free
            // If the list is empty, the shipping is free for all countries.
            $couponCountries = $coupon->getFreeShippingForCountries();

            if (!$couponCountries->isEmpty()) {
                $countryValid = false;

                /** @var CouponCountry $couponCountry */
                foreach ($couponCountries as $couponCountry) {
                    if ($countryId == $couponCountry->getCountryId()) {
                        $countryValid = true;
                        break;
                    }
                }

                if (!$countryValid) {
                    continue;
                }
            }

            // Check if shipping method is on the list of methods for which delivery is free
            // If the list is empty, the shipping is free for all methods.
            $couponModules = $coupon->getFreeShippingForModules();

            if (!$couponModules->isEmpty()) {
                $moduleValid = false;

                /** @var CouponModule $couponModule */
                foreach ($couponModules as $couponModule) {
                    if ($deliveryModuleId == $couponModule->getModuleId()) {
                        $moduleValid = true;
                        break;
                    }
                }

                if (!$moduleValid) {
                    continue;
                }
            }

            // All conditions are met, the shipping is free !
            return true;
        }

        return false;
    }
}
