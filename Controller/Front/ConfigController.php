<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use OpenApi\OpenApi;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ConfigQuery;

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
    public function getConfig($key)
    {
        $config = ConfigQuery::create()->filterByName($key)->findOne();
        if ($config && in_array($config->getId(), explode(',', OpenApi::getConfigValue('config_variables')))) {
            return new JsonResponse($config->getValue());
        }

        return new JsonResponse(Translator::getInstance()->trans('You are not allowed to access this config'), 401);
    }
}
