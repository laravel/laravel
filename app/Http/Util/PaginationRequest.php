<?php

namespace App\Http\Util;

use App\Infrastructure\Util\PaginationData;
use Illuminate\Http\Request;

trait PaginationRequest
{
    /**
     * Creates a PaginationData from the Request fields.
     * Default pageSize is 15 but if pageSize parameter is sent empty, limit will be removed.
     *
     * @param Request $request
     *
     * @return PaginationData
     */
    public function paginationData(Request $request): PaginationData
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $orderBy = $request->input('sort');
        $orderSense = $request->input('direction');

        return $this->createPaginationData($limit, $page, $orderBy, $orderSense);
    }

    /**
     * Creates a PaginationData from the Request sorting fields and without limit.
     */
    public function paginationDataWithoutLimit(Request $request): PaginationData
    {
        $orderBy = $request->input('sort');
        $orderSense = $request->input('direction');

        return $this->createPaginationData(null, 1, $orderBy, $orderSense);
    }

    /**
     * Creates a PaginationData from the Request fields in Backoffice.
     * Default pageSize is 15 but if pageSize parameter is sent empty, limit will be removed.
     *
     * @param Request $request
     *
     * @return PaginationData
     */
    public function paginationBackofficeData(Request $request): PaginationData
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $orderBy = $request->input('sort_by');
        $orderSense = $request->input('sort_sense');

        $paginationData = $this->createPaginationData($limit, $page, $orderBy, $orderSense);

        return $paginationData;
    }

    /**
     * Creates a PaginationBackofficeData from the Request sorting fields and without limit.
     */
    public function paginationBackofficeDataWithoutLimit(Request $request): PaginationData
    {
        $orderBy = $request->input('sort_by');
        $orderSense = $request->input('sort_sense');

        return $this->createPaginationData(null, 1, $orderBy, $orderSense);
    }

    private function createPaginationData($limit, $page, $orderBy, $orderSense): PaginationData
    {
        if ($limit == '') {
            $limit = null;
            $page = 1;
        }

        $paginationData = new PaginationData($limit, $page);

        if ($orderBy) {
            $paginationData->addSort($orderBy, $orderSense);
        }

        return $paginationData;
    }
}
