<?php

namespace OpenApi\Controller\Front;

use Exception;
use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use OpenApi\Service\SearchService;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ContentQuery;

/**
 * @Route("/content", name="content")
 */
class ContentController extends BaseFrontOpenApiController
{
    /**
     * @Route("/search", name="content_search", methods="GET")
     *
     * @OA\Get(
     *     path="/content/search",
     *     tags={"Content", "Search"},
     *     summary="Search contents",
     *     @OA\Parameter(
     *          name="id",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="ids[]",
     *          in="query",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  type="integer"
     *              )
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="visible",
     *          in="query",
     *          @OA\Schema(
     *              type="boolean",
     *              default="true"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="locale",
     *          in="query",
     *          description="Current locale by default",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="title",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="description",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="chapo",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="postscriptum",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *              default="20"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="order",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"alpha", "alpha_reverse", "created_at", "created_at_reverse"},
     *              default="alpha"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/Content"
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
    public function search(
        Request $request,
        ModelFactory $modelFactory,
        SearchService $searchService
    ) {
        $query = $searchService->baseSearchItems("content", $request);
        $contents = $query->find();

        return OpenApiService::jsonResponse(
            array_map(fn ($content) => $modelFactory->buildModel('Content', $content), iterator_to_array($contents))
        );
    }


    /**
     * @Route("/{id}", name="get_content", methods="GET", requirements={"collectionId"="\d+"})
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
     *
     * @throws Exception
     */
    public function getContent(ModelFactory $modelFactory, $id)
    {
        $content = ContentQuery::create()
            ->findOneById($id);
        $apiContent = $modelFactory->buildModel('Content', $content);

        if (null === $content) {
            throw new Exception(Translator::getInstance()->trans('Content does not exist.', [], OpenApi::DOMAIN_NAME));
        }

        return new JsonResponse($apiContent);
    }
}
