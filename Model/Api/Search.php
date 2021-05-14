<?php

namespace OpenApi\Model\Api;

/**
 * Class Search.
 *
 * @OA\Schema(
 *     description="A search response"
 * )
 */
class Search extends BaseApiModel
{
    /**
     * @var Result
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Result"
     *     )
     * )
     */
    protected $results;

    /**
     * @var int
     * @OA\Property(
     *     type="number",
     *     format="integer"
     * )
     */
    protected $page;

    /**
     * @var int
     * @OA\Property(
     *     type="number",
     *     format="integer",
     *     description="The total of results for current page"
     * )
     */
    protected $pageTotal;

    /**
     * @var int
     * @OA\Property(
     *     type="number",
     *     format="integer",
     *     description="The whole total of results (without paging)"
     * )
     */
    protected $total;

    /**
     * @return Result
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param Result $results
     *
     * @return Search
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return Search
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageTotal()
    {
        return $this->pageTotal;
    }

    /**
     * @param int $pageTotal
     *
     * @return Search
     */
    public function setPageTotal($pageTotal)
    {
        $this->pageTotal = $pageTotal;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return Search
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }
}
