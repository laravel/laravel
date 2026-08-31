<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\TabCompletion\Matcher;

abstract class AbstractDefaultParametersMatcher extends AbstractContextAwareMatcher
{
    /**
     * @param \ReflectionParameter[] $reflectionParameters
     *
     * @return array
     */
    public function getDefaultParameterCompletion(array $reflectionParameters): array
    {
        $parametersProcessed = [];

        foreach ($reflectionParameters as $parameter) {
            if (!$parameter->isDefaultValueAvailable()) {
                return [];
            }

            $defaultValue = $this->valueToShortString($parameter->getDefaultValue());

            $parametersProcessed[] = \sprintf('$%s = %s', $parameter->getName(), $defaultValue);
        }

        if (empty($parametersProcessed)) {
            return [];
        }

        return [\implode(', ', $parametersProcessed).')'];
    }

    /**
     * Takes in the default value of a parameter and turns it into a
     *  string representation that fits inline.
     * This is not 100% true to the original (newlines are inlined, for example).
     *
     * @param mixed $value
     */
    private function valueToShortString($value): string
    {
        if (!\is_array($value)) {
            return \json_encode($value);
        }

        $chunks = [];
        $chunksSequential = [];

        $allSequential = true;

        foreach ($value as $key => $item) {
            $allSequential = $allSequential && \is_numeric($key) && $key === \count($chunksSequential);

            $keyString = $this->valueToShortString($key);
            $itemString = $this->valueToShortString($item);

            $chunks[] = "{$keyString} => {$itemString}";
            $chunksSequential[] = $itemString;
        }

        $chunksToImplode = $allSequential ? $chunksSequential : $chunks;

        return '['.\implode(', ', $chunksToImplode).']';
    }
}
