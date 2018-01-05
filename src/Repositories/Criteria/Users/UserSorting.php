<?php

namespace ProjectName\Repositories\Criteria\Users;

use Digbang\Utils\Sorting;

class UserSorting extends Sorting
{
    public const EMAIL = 'email';
    public const FIRST_NAME = 'firstName';
    public const LAST_NAME = 'lastName';
    public const USERNAME = 'username';
    public const LAST_LOGIN = 'lastLogin';
}
