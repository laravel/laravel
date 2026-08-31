<?php

declare(strict_types=1);

namespace Doctrine\Inflector;

interface WordInflector
{
    public function inflect(string $word): string;
}
