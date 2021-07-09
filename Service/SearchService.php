<?php


namespace OpenApi\Service;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\HttpFoundation\Request;

class SearchService
{
    public function baseSearchItems($itemType, Request $request): ModelCriteria
    {
        $queryClassName = "Thelia\\Model\\".ucfirst($itemType)."Query";
        /** @var ModelCriteria $itemQuery */
        $itemQuery = $queryClassName::create();

        if (null !== $id = $request->get('id')) {
            $itemQuery->filterById($id);
        }

        if (null !== $ids = $request->get('ids')) {
            $itemQuery->filterById($ids, Criteria::IN);
        }

        if ((null !== $parentsIds = $request->get('parentsIds')) && method_exists($itemQuery, "filterByParent")) {
            $itemQuery->filterByParent($parentsIds, Criteria::IN);
        }

        $itemQuery->filterByVisible((bool) json_decode(json_encode($request->get('visible', true))));

        $order = $request->get('order', 'alpha');
        $locale = $request->get('locale', $request->getSession()->getLang()->getLocale());
        $title = $request->get('title');
        $description = $request->get('description');
        $chapo = $request->get('chapo');
        $postscriptum = $request->get('postscriptum');

        $itemQuery
            ->limit($request->get('limit', 20))
            ->offset($request->get('offset', 0));

        switch ($order) {
            case 'created':
                $itemQuery->orderByCreatedAt();
                break;
            case 'created_reverse':
                $itemQuery->orderByCreatedAt(Criteria::DESC);
                break;
        }

        if (null !== $title || null !== $description || null !== $chapo || null !== $postscriptum) {
            $useI18nMethodName = "use".ucfirst($itemType)."I18nQuery";
            $itemI18nQuery = $itemQuery
                ->$useI18nMethodName()
                ->filterByLocale($locale);

            if (null !== $title) {
                $itemI18nQuery->filterByTitle('%'.$title.'%', Criteria::LIKE);
            }

            if (null !== $description) {
                $itemI18nQuery->filterByDescription('%'.$description.'%', Criteria::LIKE);
            }

            if (null !== $chapo) {
                $itemI18nQuery->filterByChapo('%'.$chapo.'%', Criteria::LIKE);
            }

            if (null !== $postscriptum) {
                $itemI18nQuery->filterByPostscriptum('%'.$postscriptum.'%', Criteria::LIKE);
            }

            switch ($order) {
                case 'alpha':
                    $itemI18nQuery->orderByTitle();
                    break;
                case 'alpha_reverse':
                    $itemI18nQuery->orderByTitle(Criteria::DESC);
                    break;
            }

            $itemI18nQuery->endUse();
        }

        return $itemQuery;
    }
}
