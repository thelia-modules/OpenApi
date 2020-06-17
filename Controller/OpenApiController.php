<?php

namespace OpenApi\Controller;

use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\HttpFoundation\JsonResponse;
use function OpenApi\scan;

/**
 * @OA\Info(title="Thelia Open Api", version="0.1")
 * @OA\SecurityScheme(
 *          securityScheme="customerAuth",
 *          type="http",
 *          scheme="bearer",
 *          bearerFormat="JWT"
 * )
 */
class OpenApiController extends BaseOpenApiController
{
    /**
     * @Route("/doc", name="documentation")
     */
    public function getDocumentation()
    {
        $controllerApi = scan([__DIR__.'/../Controller', __DIR__.'/../Model']);

        $annotations = json_decode($controllerApi->toJson(), true);


        $annotations['servers'] = [["url" => "http://localhost:8080/open_api"]];

        return new JsonResponse($annotations);

    }
}