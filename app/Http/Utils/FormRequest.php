<?php

namespace App\Http\Utils;

use Illuminate\Foundation\Http\FormRequest as Request;
use ProjectName\Repositories\ReadRepository;

abstract class FormRequest extends Request
{
    private function getRepository(string $className): ?ReadRepository
    {
        $repository = $this->container->make($className);

        if ($repository instanceof ReadRepository) {
            return $repository;
        }

        throw new \DomainException('The class not implements ReadRepositoy');
    }
}
