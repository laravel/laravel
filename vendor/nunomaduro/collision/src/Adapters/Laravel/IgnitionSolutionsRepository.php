<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use NunoMaduro\Collision\Contracts\SolutionsRepository;
use Spatie\ErrorSolutions\Contracts\SolutionProviderRepository;
use Spatie\Ignition\Contracts\SolutionProviderRepository as IgnitionSolutionProviderRepository;
use Throwable;

/**
 * @internal
 */
final class IgnitionSolutionsRepository implements SolutionsRepository
{
    /**
     * Holds an instance of ignition solutions provider repository.
     *
     * @var IgnitionSolutionProviderRepository|SolutionProviderRepository
     */
    protected $solutionProviderRepository; // @phpstan-ignore-line

    /**
     * IgnitionSolutionsRepository constructor.
     */
    public function __construct(IgnitionSolutionProviderRepository|SolutionProviderRepository $solutionProviderRepository) // @phpstan-ignore-line
    {
        $this->solutionProviderRepository = $solutionProviderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromThrowable(Throwable $throwable): array // @phpstan-ignore-line
    {
        return $this->solutionProviderRepository->getSolutionsForThrowable($throwable); // @phpstan-ignore-line
    }
}
