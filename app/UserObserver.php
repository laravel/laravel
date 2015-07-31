<?php

namespace App;

class UserObserver
{
    public function saving($model)
    {
        $model->observed = true;
    }
}
