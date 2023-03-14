<?php

namespace OpenApi\Controller\Front;

use Exception;
use OpenApi\OpenApi;
use OpenApi\Service\SearchService;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CategoryQuery;

/**
 * @Route("/category", name="category")
 */
class CategoryController extends BaseFrontOpenApiController
{
    /**
     * @Route("/search", name="category_search", methods="GET")
     *
     * @OA\Get(
     *     path="/category/search",
     *     tags={"Category", "search"},
     *     summary="Search categories",
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
     *          name="parentsIds[]",
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
     *                      ref="#/components/schemas/Category"
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
    public function getCategory(Request $request)
    {
        $modelFactory = $this->getModelFactory();

        /** @var SearchService $searchService */
        $searchService = $this->getContainer()->get('open_api.search.service');

        $query = $searchService->baseSearchItems("category", $request);
        $categories = $query->find();

        return new JsonResponse(
            array_map(
                function($category) use ($modelFactory) {
                    return $modelFactory->buildModel('Category', $category);
                },
                iterator_to_array($categories)
            )
        );
    }
}
