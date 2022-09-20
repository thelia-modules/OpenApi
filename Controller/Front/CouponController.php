<?php

namespace OpenApi\Controller\Front;

use Exception;
use OpenApi\Annotations as OA;
use OpenApi\Model\Api\Coupon;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Thelia\Core\Event\Coupon\CouponConsumeEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Translation\Translator;
use Thelia\Exception\UnmatchableConditionException;
use Thelia\Model\CouponQuery;

/**
 * @Route("/coupon", name="coupon")
 */
class CouponController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="submit_coupon", methods="POST")
     *
     * @OA\Post(
     *     path="/coupon",
     *     tags={"coupon"},
     *     summary="Submit a coupon",
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Coupon")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function submitCoupon(
        Request $request,
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory
    ) {
        $cart = $request->getSession()->getSessionCart($dispatcher);
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        /** @var Coupon $openApiCoupon */
        $openApiCoupon = $modelFactory->buildModel('Coupon', $request->getContent());
        if (null === $openApiCoupon->getCode()) {
            throw new \Exception(Translator::getInstance()->trans('Coupon code cannot be null', [], OpenApi::DOMAIN_NAME));
        }

        /** We verify that the given coupon actually exists in the base */
        $theliaCoupon = CouponQuery::create()->filterByCode($openApiCoupon->getCode())->findOne();
        if (null === $theliaCoupon) {
            throw new \Exception(Translator::getInstance()->trans('No coupons were found for this coupon code.', [], OpenApi::DOMAIN_NAME));
        }

        try {
            $event = new CouponConsumeEvent($openApiCoupon->getCode());
            $dispatcher->dispatch($event, TheliaEvents::COUPON_CONSUME);
            $openApiCoupon = $modelFactory->buildModel('Coupon', $theliaCoupon);
        } catch (UnmatchableConditionException $exception) {
            throw new \Exception(Translator::getInstance()->trans('You should sign in or register to use this coupon.', [], OpenApi::DOMAIN_NAME));
        }

        return OpenApiService::jsonResponse($openApiCoupon);
    }

    /**
     * @Route("/clear_all", name="clear_all_coupon", methods="GET")
     *
     * @OA\Get(
     *     path="/coupon/clear_all",
     *     tags={"coupon"},
     *     summary="Clear all coupons",
     *
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function clearAllCoupon(
        Request $request,
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory
    ) {
        $cart = $request->getSession()->getSessionCart($dispatcher);
        try {
            $dispatcher->dispatch((new Event()), TheliaEvents::COUPON_CLEAR_ALL);
        } catch (\Exception $exception) {
            throw new \Exception(Translator::getInstance()->trans('An error occurred while clearing coupons : ') . $exception->getMessage());
        }

        return OpenApiService::jsonResponse($modelFactory->buildModel('Cart', $cart));
    }

    /**
     * @Route("/clear/{id}", name="clear_coupon", methods="GET")
     *
     * @OA\Get(
     *     path="/coupon/clear/{id}",
     *     tags={"coupon"},
     *     summary="Clear a specific coupon from cart"
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonCocalontent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function clearCoupon(
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory,
        Session $session,
        $id
    ) {
        $cart = $session->getSessionCart($dispatcher);

        try {
            $coupon = CouponQuery::create()->findOneById($id);

            if (null === $coupon) {
                throw new Exception();
            }

            $consumedCoupons = $session->getConsumedCoupons();

            unset($consumedCoupons[$coupon->getCode()]);

            $session->setConsumedCoupons($consumedCoupons);
        } catch (Exception $e) {
            throw new Exception(Translator::getInstance()->trans('An error occurred while clearing coupon ' . $id . ' : ') . $e->getMessage());
        }

        return OpenApiService::jsonResponse($modelFactory->buildModel('Cart', $cart));
    }
}
