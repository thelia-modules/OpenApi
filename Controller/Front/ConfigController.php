<?php

namespace OpenApi\Controller\Front;

use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
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
        return new JsonResponse(ConfigQuery::read($key));
    }
}
