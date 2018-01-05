<?php

namespace ProjectName\Repositories\Criteria\Users;

use Digbang\Utils\Filter;

class UserFilter extends Filter
{
    public const EMAIL = 'email';
    public const FIRST_NAME = 'firstName';
    public const LAST_NAME = 'lastName';
    public const USERNAME = 'username';
    public const ACTIVATED = 'activated';
}
