<?php

namespace App\Http\Backoffice\Requests\Users;

use App\Http\Backoffice\Requests\BackofficeCriteriaRequest;
use ProjectName\Repositories\Criteria\Users\UserFilter;
use ProjectName\Repositories\Criteria\Users\UserSorting;

class UserCriteriaRequest extends BackofficeCriteriaRequest
{
    protected function getFilterClass(): string
    {
        return UserFilter::class;
    }

    protected function getSortingClass(): string
    {
        return UserSorting::class;
    }
}
