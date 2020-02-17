<?php

namespace App\Http\Utils;

use Doctrine\Common\Persistence\ObjectRepository;
use Illuminate\Foundation\Http\FormRequest as Request;
use ProjectName\Repositories\ReadRepository;

abstract class FormRequest extends Request
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

        throw new \DomainException('The class not implements ReadRepositoy');
    }
}
