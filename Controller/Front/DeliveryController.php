<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use OpenApi\Events\DeliveryModuleOptionEvent;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Model\Api\DeliveryModule;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\Delivery\PickupLocationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AddressQuery;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\Cart;
use Thelia\Model\CountryQuery;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Model\PickupLocation;
use Thelia\Model\StateQuery;
use Thelia\Module\AbstractDeliveryModule;
use Thelia\Module\BaseModule;
use Thelia\Module\Exception\DeliveryException;

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
    public function getPickupLocations(Request $request, EventDispatcherInterface $dispatcher)
    {
        $state = $request->get('stateId') ? (StateQuery::create())->filterById($request->get('stateId'))->findOne() : null;
        $country = $request->get('countryId') ? (CountryQuery::create())->filterById($request->get('countryId'))->findOne() : null;
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

        $dispatcher->dispatch($pickupLocationEvent, TheliaEvents::MODULE_DELIVERY_GET_PICKUP_LOCATIONS);

        return OpenApiService::jsonResponse(
            array_map(
                fn (PickupLocation $pickupLocation) => $pickupLocation->toArray(),
                $pickupLocationEvent->getLocations()
            )
        );
    }

    /**
     * @Route("/simple-modules", name="delivery_simple_modules", methods="GET")
     *
     * @OA\Get(
     *     path="/delivery/simple-modules",
     *     tags={"delivery", "modules"},
     *     summary="List all delivery modules as simple list (without postages and options)",
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
    public function getSimpleDeliveryModules(ModelFactory $modelFactory)
    {
        $modules = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE)
            ->find();

        return OpenApiService::jsonResponse(
            array_map(
                function (Module $module) use ($modelFactory) {
                    /** @var AbstractDeliveryModule $moduleInstance */
                    $moduleInstance = $module->getDeliveryModuleInstance($this->container);

                    /** @var DeliveryModule $deliveryModule */
                    $deliveryModule = $modelFactory->buildModel('DeliveryModule', $module);
                    $deliveryModule->setDeliveryMode($moduleInstance->getDeliveryMode());

                    return $deliveryModule;
                },
                iterator_to_array($modules)
            )
        );
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
    public function getDeliveryModules(
        Request $request,
        SecurityContext $securityContext,
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory
    ) {
        $deliveryAddress = $this->getDeliveryAddress($request, $securityContext);

        $cart = $request->getSession()->getSessionCart($dispatcher);
        $country = $deliveryAddress ? $deliveryAddress->getCountry() : CountryQuery::create()->filterByByDefault(1)->findOne();

        if (null === $country) {
            throw new \Exception(Translator::getInstance()->trans('You must either pass an address id or have a customer connected', [], OpenApi::DOMAIN_NAME));
        }

        $state = $deliveryAddress ? $deliveryAddress->getState() : StateQuery::create()->filterByCountryId($country->getId())->findOne();

        $moduleQuery = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE);

        if (null !== $moduleId = $request->get('moduleId')) {
            $moduleQuery->filterById($moduleId);
        }

        $modules = $moduleQuery->find();

        $class = $this;

        return OpenApiService::jsonResponse(
            array_map(
                fn ($module) => $class->getDeliveryModule($module, $dispatcher, $cart, $modelFactory, $deliveryAddress, $country, $state),
                iterator_to_array($modules)
            )
        );
    }

    /**
     * @Route("/set-delivery", name="set_delivery_modules", methods="GET")
     *
     * @OA\Get(
     *     path="/delivery/set-delivery",
     *     tags={"delivery", "modules"},
     *     summary="Set delivery module on session to calculate postage",
     *     @OA\Parameter(
     *          name="delivery_module_id",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function setDeliveryModules(Request $request)
    {
        $deliveryModuleId = $request->get('delivery_module_id');
        $session = $request->getSession();
        $order = $session->getOrder();

        if (!$order) {
            return new JsonResponse();
        }

        $order->setDeliveryModuleId($deliveryModuleId);
        $session->setOrder($order);

        return new JsonResponse();
    }

    protected function getDeliveryModule(
        Module $theliaDeliveryModule,
        EventDispatcherInterface $dispatcher,
        Cart $cart,
        ModelFactory $modelFactory,
        $address,
        $country,
        $state
    ) {
        $areaDeliveryModule = AreaDeliveryModuleQuery::create()
            ->findByCountryAndModule($country, $theliaDeliveryModule, $state);
        $isCartVirtual = $cart->isVirtual();

        $isValid = true;
        if (false === $isCartVirtual && null === $areaDeliveryModule) {
            $isValid = false;
        }

        $moduleInstance = $theliaDeliveryModule->getDeliveryModuleInstance($this->container);

        if (true === $isCartVirtual && false === $moduleInstance->handleVirtualProductDelivery()) {
            $isValid = false;
        }

        $deliveryPostageEvent = new DeliveryPostageEvent($moduleInstance, $cart, $address, $country, $state);
        try {
            $dispatcher->dispatch(
                $deliveryPostageEvent,
                TheliaEvents::MODULE_DELIVERY_GET_POSTAGE
            );
        } catch (DeliveryException $exception) {
            $isValid = false;
        }

        if (!$deliveryPostageEvent->isValidModule()) {
            $isValid = false;
        }

        $deliveryModuleOptionEvent = new DeliveryModuleOptionEvent($theliaDeliveryModule, $address, $cart, $country, $state);

        $dispatcher->dispatch(
            $deliveryModuleOptionEvent,
            OpenApiEvents::MODULE_DELIVERY_GET_OPTIONS
        );

        /** @var DeliveryModule $deliveryModule */
        $deliveryModule = $modelFactory->buildModel('DeliveryModule', $theliaDeliveryModule);

        $deliveryModule
            ->setDeliveryMode($deliveryPostageEvent->getDeliveryMode())
            ->setValid($isValid)
            ->setOptions($deliveryModuleOptionEvent->getDeliveryModuleOptions())
        ;

        return $deliveryModule;
    }

    protected function getDeliveryAddress(Request $request, SecurityContext $securityContext)
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
        $currentCustomer = $securityContext->getCustomerUser();

        if (null === $currentCustomer) {
            return null;
        }

        return $currentCustomer->getDefaultAddress();
    }
}
