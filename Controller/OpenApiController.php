<?php

namespace OpenApi\Controller;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\BaseController;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Model\CustomerQuery;
use function OpenApi\scan;

/**
 * @OA\Info(title="Thelia Open Api", version="0.1")
 * @OA\SecurityScheme(
 *          securityScheme="cookieAuth",
 *          type="apiKey",
 *          in="cookie",
 *          name="PHPSESSID"
 * )
 * @OA\OpenApi(
 *      security={
 *         {"cookieAuth": {}}
 *      }
 * )
 */
class OpenApiController extends BaseFrontController
{
    /**
     * @Route("/doc", name="documentation")
     */
    public function getDocumentation()
    {
        header("Access-Control-Allow-Origin: *");

        $controllerApi = scan([__DIR__.'/../Controller', __DIR__.'/../Model']);

        $annotations = json_decode($controllerApi->toJson(), true);


        $annotations['servers'] = [
            ["url" => "http://localhost:8080/open_api"],
            ["url" => "http://localhost:8080/index_dev.php/open_api"]
        ];

        return new JsonResponse($annotations);

    }

}