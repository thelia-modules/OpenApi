<?php

namespace OpenApi\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Response;
use OpenApi\Annotations as OA;

/**
 * @Route("/checkout")
 */
class CheckoutController extends BaseFrontController
{
    /**
     * @Route("", name="set_checkout", methods="POST")
     * @OA\Post(
     *     path="/checkout",
     *     tags={"checkout"},
     *     summary="Validate and set an checkout",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Checkout")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Checkout")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function setCheckout()
    {
        return Response::create("Helllo");
    }

    /**
     * @Route("", name="get_checkout", methods="GET")
     * @OA\Get(
     *     path="/open_api/checkout",
     *     tags={"checkout"},
     *     summary="get current checkout",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Checkout"),
     *     )
     * )
     */
    public function getCheckout()
    {
        return JsonResponse::create([
            'state' => 'VALID',
            'cartId' => 1,
            'deliveryModuleId' => 1,
            'paymentModuleId' => 1,
            'billingAddressId' => 1,
            'deliveryAddressId' => 1
        ]);
    }
}