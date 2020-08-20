<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
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
    public function search(Request $request)
    {
        $productQuery = ProductQuery::create();

        if (null !== $id = $this->getRequestValue('id')) {
            $productQuery->filterById($id);
        }

        if (null !== $reference = $this->getRequestValue('reference')) {
            $productQuery->filterByRef($reference);
        }

        $productQuery->filterByVisible($this->getRequestValue('visible', true));

        $order = $this->getRequestValue('order', 'alpha');
        $locale = $this->getRequestValue('locale', $request->getLocale());
        $title = $this->getRequestValue('title');
        $description = $this->getRequestValue('description');
        $chapo = $this->getRequestValue('chapo');
        $postscriptum = $this->getRequestValue('postscriptum');

//        $productQuery->joinWithProductI18n();
//
//        if (null !== $title || null !== $description || null !== $chapo || null !== $postscriptum) {
//            $productI18nQuery = $productQuery
//                ->useProductI18nQuery()
//                ->filterByLocale($locale);
//
//            if (null !== $title) {
//                $productI18nQuery->filterByTitle('%'.$title.'%', Criteria::LIKE);
//            }
//
//            if (null !== $description) {
//                $productI18nQuery->filterByDescription('%'.$description.'%', Criteria::LIKE);
//            }
//
//            if (null !== $chapo) {
//                $productI18nQuery->filterByChapo('%'.$chapo.'%', Criteria::LIKE);
//            }
//
//            if (null !== $postscriptum) {
//                $productI18nQuery->filterByPostscriptum('%'.$postscriptum.'%', Criteria::LIKE);
//            }
//
//            $productI18nQuery->endUse();
//        }

        $productQuery->limit($this->getRequestValue('limit', 20))
            ->offset($this->getRequestValue('offset', 0));

//        switch ($order) {
//            case 'alpha' :
//                $productQuery->addAscendingOrderByColumn('product_i18n.title');
//                break;
//            case 'alpha_reverse' :
//                $productQuery->addDescendingOrderByColumn('product_i18n.title');
//                break;
//            case 'created' :
//                $productQuery->orderByCreatedAt();
//                break;
//            case 'created_reverse' :
//                $productQuery->orderByCreatedAt(Criteria::DESC);
//                break;
//        }

        $products = $productQuery->find();
        $modelFactory = $this->getModelFactory();

        $products = array_map(function ($product) use ($modelFactory){
            return $modelFactory->buildModel('Product', $product);
        }, iterator_to_array($products));

        return $this->jsonResponse($products);
    }
}