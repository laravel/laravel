<?php

namespace App\Http\Backoffice\Requests;

use Digbang\Utils\CriteriaRequest;

abstract class BackofficeCriteriaRequest extends CriteriaRequest
{
    protected function buildSorting(): array
    {
        $sort = $this->request->input('sort_by');
        $direction = $this->request->input('sort_sense', 'ASC');

        return $sort ? [$sort => $direction] : [];
    }
}
