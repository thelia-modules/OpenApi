<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Controller\Front\BaseFrontController;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\Event\Delivery\PickupLocationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Model\CountryQuery;
use Thelia\Model\PickupLocation;
use Thelia\Model\StateQuery;

/**
 * @Route("/delivery", name="delivery")
 */
class DeliveryController extends BaseFrontController
{
    /**
     * @Route("/pickup-locations", name="deliver_pickup_locations", methods="GET")
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
     *          description="Radius in km to filter pickup locations",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="moduleIds",
     *          description="For filter pickup locations by modules",
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
        $state = $request->get('stateId') ? (StateQuery::create())->findOneById($request->get('stateId')) : null;
        $country = $request->get('countryId') ? (CountryQuery::create())->findOneById($request->get('countryId')) : null;
        $pickupLocationEvent = new PickupLocationEvent(
            null,
            null,
            $request->get('moduleIds'),
            $request->get('address'),
            $request->get('city'),
            $request->get('zipCode'),
            $state,
            $country
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
    }
}