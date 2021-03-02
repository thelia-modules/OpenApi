<?php

namespace OpenApi\Controller\Front;

use Exception;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use Thelia\Core\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ContentQuery;

/**
 * @Route("/content", name="content")
 */
class ContentController extends BaseFrontOpenApiController
{
    /**
     * @Route("/{id}", name="get_content", methods="GET")
     *
     * @OA\Get(
     *     path="/content/{id}",
     *     tags={"content"},
     *     summary="Get content values by ID",
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
     *          @OA\JsonContent(ref="#/components/schemas/Content")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     * @throws Exception
     */
    public function getContent(ModelFactory $modelFactory, $id)
    {
        $content = ContentQuery::create()
            ->findOneById($id);
        $apiContent = $modelFactory->buildModel('Content', $content);

        if (null === $content) {
            throw new Exception(Translator::getInstance()->trans("Content does not exist.", [], OpenApi::DOMAIN_NAME));
        }

        return new JsonResponse($apiContent);
    }
}
