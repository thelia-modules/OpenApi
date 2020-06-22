<?php

namespace OpenApi\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Response;
use OpenApi\Annotations as OA;

/**
 * @Route("/address", name="address")
 */
class AddressController extends BaseFrontController
{
    /**
     * @Route("", name="address", methods="GET")
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
    public function address(Request $request)
    {

    }
}