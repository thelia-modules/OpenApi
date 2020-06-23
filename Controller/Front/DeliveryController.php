<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\DeliveryModule;
use OpenApi\Model\Api\Error;
use OpenApi\OpenApi;
use Thelia\Controller\Front\BaseFrontController;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\Delivery\PickupLocationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AddressQuery;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Model\PickupLocation;
use Thelia\Model\StateQuery;
use Thelia\Module\BaseModule;

/**
 * @Route("/delivery", name="delivery")
 */
class DeliveryController extends BaseFrontOpenApiController
{
    /**
     * @Route("/pickup-locations", name="delivery_pickup_locations", methods="GET")
     *
     * @OA\Get(
     *     path="/delivery/pickup-locations",
     *     tags={"delivery"},
     *     summary="Get the list of all available pickup locations for a specific address, by default from all modules or filtered by an array of delivery modules id",
     *     @OA\Parameter(
     *          name="address",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="city",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="zipCode",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="stateId",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="countryId",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="radius",
     *          description="Radius in meters to filter pickup locations",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="maxRelays",
     *          description="Max number of relays returned by the module, if applicable",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="orderWeight",
     *          description="Total weight of the order in grams (eg: 1000 for 1kg)",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="moduleIds[]",
     *          description="To filter pickup locations by modules",
     *          in="query",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  type="integer"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/PickupLocation"
     *                  )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getPickupLocations(Request $request)
    {
        try {
            $state = $request->get('stateId') ? (StateQuery::create())->findOneById($request->get('stateId')) : null;
            $country = $request->get('countryId') ? (CountryQuery::create())->findOneById($request->get('countryId')) : null;
            $pickupLocationEvent = new PickupLocationEvent(
                null,
                $request->get('radius'),
                $request->get('maxRelays'),
                $request->get('address'),
                $request->get('city'),
                $request->get('zipCode'),
                $request->get('orderWeight'),
                $state,
                $country,
                $request->get('moduleIds')
            );

            $this->getDispatcher()->dispatch(TheliaEvents::MODULE_DELIVERY_GET_PICKUP_LOCATIONS, $pickupLocationEvent);

            return new JsonResponse(
                array_map(
                    function (PickupLocation $pickupLocation) {
                        return $pickupLocation->toArray();
                    },
                    $pickupLocationEvent->getLocations()
                )
            );
        } catch (\Exception $e) {
            $error = new Error(
                Translator::getInstance()->trans('Error for retrieving pickup locations', [], OpenApi::DOMAIN_NAME),
                $e->getMessage()
            );

            return new JsonResponse(
                $error
            );
        }
    }

    /**
     * @Route("/modules", name="delivery_modules", methods="GET")
     *
     * @OA\Get(
     *     path="/delivery/modules",
     *     tags={"delivery", "modules"},
     *     summary="List all available delivery modules",
     *     @OA\Parameter(
     *          name="addressId",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="moduleId",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/DeliveryModule"
     *                  )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getDeliveryModules(Request $request)
    {
        $deliveryAddress = $this->getDeliveryAddress($request);

        if (null === $deliveryAddress) {
            throw new \Exception(Translator::getInstance()->trans('You must either pass an address id or have a customer connected', [], OpenApi::DOMAIN_NAME));
        }

        $cart = $request->getSession()->getSessionCart($this->getDispatcher());
        $country = $deliveryAddress->getCountry();
        $state = $deliveryAddress->getState();

        $moduleQuery = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE);

        if (null !== $moduleId = $request->get('moduleId')) {
            $moduleQuery->filterById($moduleId);
        }

        $modules = $moduleQuery->find();

        $class = $this;
        return new JsonResponse(
            array_map(
                function ($module) use ($class, $cart, $deliveryAddress, $country, $state)  {
                    return $class->getDeliveryModule($module, $cart, $deliveryAddress, $country, $state);
                },
                iterator_to_array($modules)
            )
        );
    }

    protected function getDeliveryModule(Module $deliveryModule, $cart, $address, $country, $state)
    {
        $areaDeliveryModule = AreaDeliveryModuleQuery::create()
            ->findByCountryAndModule($country, $deliveryModule, $state);
        $isCartVirtual = $cart->isVirtual();

        $isValid = true;
        if (false === $isCartVirtual && null === $areaDeliveryModule) {
            $isValid = false;
        }

        $moduleInstance = $deliveryModule->getDeliveryModuleInstance($this->container);

        if (true === $isCartVirtual && false === $moduleInstance->handleVirtualProductDelivery()
        ) {
            $isValid = false;
        }

        $deliveryPostageEvent = new DeliveryPostageEvent($moduleInstance, $cart, $address, $country, $state);
        $this->getDispatcher()->dispatch(
            TheliaEvents::MODULE_DELIVERY_GET_POSTAGE,
            $deliveryPostageEvent
        );

        if (!$deliveryPostageEvent->isValidModule()) {
            $isValid = false;
        }

        $deliveryModule =  (new DeliveryModule())
            ->setValid($isValid)
            ->setDeliveryMode($deliveryPostageEvent->getDeliveryMode())
            ->setId($deliveryModule->getId())
            ->setCode($deliveryModule->getCode())
            ->setTitle($deliveryModule->getTitle())
            ->setDescription($deliveryModule->getDescription())
            ->setChapo($deliveryModule->getChapo())
            ->setPostscriptum($deliveryModule->getPostscriptum());

        if ($isValid) {
            $deliveryModule
                ->setMinimumDeliveryDate($deliveryPostageEvent->getMinimumDeliveryDate())
                ->setMaximumDeliveryDate($deliveryPostageEvent->getMaximumDeliveryDate())
                ->setPostage($deliveryPostageEvent->getPostage()->getAmount())
                ->setPostageTax($deliveryPostageEvent->getPostage()->getAmountTax())
                ->setPostageUntaxed($deliveryPostageEvent->getPostage()->getAmount() - $deliveryPostageEvent->getPostage()->getAmountTax());
        }

        return $deliveryModule;
    }

    protected function getDeliveryAddress(Request $request)
    {
        $addressId = $request->get('addressId');

        if (null === $addressId) {
            $addressId = $request->getSession()->getOrder()->getChoosenDeliveryAddress();
        }

        if (null !== $addressId) {
            $address = AddressQuery::create()->findPk($addressId);
            if (null !== $address) {
                return $address;
            }
        }

        // If no address in request or in order take customer default address
        $currentCustomer = $this->getSecurityContext()->getCustomerUser();

        if (null === $currentCustomer) {
            return null;
        }

        return $currentCustomer->getDefaultAddress();
    }
}