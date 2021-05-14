<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\Service\OpenApiService;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\ProductQuery;

/**
 * @Route("/product", name="product")
 */
class ProductController extends BaseFrontOpenApiController
{
    /**
     * @Route("/search", name="product_search", methods="GET")
     *
     * @OA\Get(
     *     path="/product/search",
     *     tags={"product", "search"},
     *     summary="Search products",
     *     @OA\Parameter(
     *          name="id",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="reference",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
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
     *                      ref="#/components/schemas/Product"
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
        ModelFactory $modelFactory
    ) {
        $productQuery = ProductQuery::create();

        if (null !== $id = $request->get('id')) {
            $productQuery->filterById($id);
        }

        if (null !== $reference = $request->get('reference')) {
            $productQuery->filterByRef($reference);
        }

        $productQuery->filterByVisible($request->get('visible', true));

        $order = $request->get('order', 'alpha');
        $locale = $request->get('locale', $request->getSession()->getLang()->getLocale());
        $title = $request->get('title');
        $description = $request->get('description');
        $chapo = $request->get('chapo');
        $postscriptum = $request->get('postscriptum');

        $productQuery
            ->limit($request->get('limit', 20))
            ->offset($request->get('offset', 0));

        switch ($order) {
            case 'created':
                $productQuery->orderByCreatedAt();
                break;
            case 'created_reverse':
                $productQuery->orderByCreatedAt(Criteria::DESC);
                break;
        }

        if (null !== $title || null !== $description || null !== $chapo || null !== $postscriptum) {
            $productI18nQuery = $productQuery
                ->useProductI18nQuery()
                ->filterByLocale($locale);

            if (null !== $title) {
                $productI18nQuery->filterByTitle('%'.$title.'%', Criteria::LIKE);
            }

            if (null !== $description) {
                $productI18nQuery->filterByDescription('%'.$description.'%', Criteria::LIKE);
            }

            if (null !== $chapo) {
                $productI18nQuery->filterByChapo('%'.$chapo.'%', Criteria::LIKE);
            }

            if (null !== $postscriptum) {
                $productI18nQuery->filterByPostscriptum('%'.$postscriptum.'%', Criteria::LIKE);
            }

            switch ($order) {
                case 'alpha':
                    $productI18nQuery->orderByTitle();
                    break;
                case 'alpha_reverse':
                    $productI18nQuery->orderByTitle(Criteria::DESC);
                    break;
            }

            $productI18nQuery->endUse();
        }

        $products = $productQuery->find();

        $products = array_map(fn ($product) => $modelFactory->buildModel('Product', $product), iterator_to_array($products));

        return OpenApiService::jsonResponse($products);
    }
}
