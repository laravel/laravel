<?php

declare(strict_types=1);

namespace Dotenv\Loader;

use Dotenv\Repository\RepositoryInterface;

interface LoaderInterface
{
    /**
     * Load the given entries into the repository.
     *
     * @param \Dotenv\Repository\RepositoryInterface $repository
     * @param \Dotenv\Parser\Entry[]                 $entries
     *
     * @return array<string, string|null>
     */
    public function load(RepositoryInterface $repository, array $entries);
}
