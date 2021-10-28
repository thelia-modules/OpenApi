<?php

namespace OpenApi\Controller\Front;

use OpenApi\OpenApi;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\Cart;
use Thelia\Model\CartItemQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Country;
use Thelia\Model\CouponQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElements;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Model\State;
use Thelia\Module\BaseModule;
use Thelia\Module\Exception\DeliveryException;

/**
 * @Route("/config", name="config")
 */
class ConfigController extends BaseFrontOpenApiController
{
    /**
     * @Route("/{key}", name="get_config", methods="GET")
     *
     * @OA\Get(
     *     path="/config/{key}",
     *     tags={"config"},
     *     summary="Get a config value by it's key",
     *      @OA\Parameter(
     *          name="key",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(type="string")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getConfig(Request $request, $key)
    {
        $config = ConfigQuery::create()->filterByName($key)->findOne();
        if ($config && in_array($config->getId(), explode(',', OpenApi::getConfigValue('config_variables')))) {
            return new JsonResponse($config->getValue());
        }

        return new JsonResponse(Translator::getInstance()->trans('You are not allowed to access this config'), 401);
    }
}
