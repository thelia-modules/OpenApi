<?php


namespace OpenApi\Controller\Front;


use Front\Front;
use OpenApi\Model\Api\Coupon;
use OpenApi\Model\Api\Error;
use OpenApi\OpenApi;
use Thelia\Core\Event\Coupon\CouponConsumeEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
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
    public function submitCoupon(Request $request)
    {
        $cart = $request->getSession()->getSessionCart($this->getDispatcher());
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        /** @var Coupon $openApiCoupon */
        $openApiCoupon = $this->getModelFactory()->buildModel('Coupon', $request->getContent());
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
            $this->getDispatcher()->dispatch(TheliaEvents::COUPON_CONSUME, $event);
            $openApiCoupon = $this->getModelFactory()->buildModel('Coupon', $theliaCoupon);
        } catch (UnmatchableConditionException $exception) {
            throw new \Exception(Translator::getInstance()->trans('You should sign in or register to use this coupon.', [], OpenApi::DOMAIN_NAME));
        }

        return $this->jsonResponse($openApiCoupon);
    }
}