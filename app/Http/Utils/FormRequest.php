<?php

namespace App\Http\Utils;

use Doctrine\Common\Persistence\ObjectRepository;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use ProjectName\Repositories\ReadRepository;

abstract class FormRequest extends BaseFormRequest
{
    /**
     * @param string $className
     * @return ReadRepository|ObjectRepository
     */
    protected function repository(string $className)
    {
        $repository = $this->container->make($className);

        if ($repository instanceof ReadRepository || $repository instanceof  ObjectRepository) {
            return $repository;
        }

        throw new \RuntimeException("$className must implement ReadRepository or ObjectRepository");
    }
}
