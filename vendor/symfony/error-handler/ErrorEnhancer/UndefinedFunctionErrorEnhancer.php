<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler\ErrorEnhancer;

use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\ErrorHandler\Error\UndefinedFunctionError;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UndefinedFunctionErrorEnhancer implements ErrorEnhancerInterface
{
    public function enhance(\Throwable $error): ?\Throwable
    {
        if ($error instanceof FatalError) {
            return null;
        }

        $message = $error->getMessage();
        $messageLen = \strlen($message);
        $notFoundSuffix = '()';
        $notFoundSuffixLen = \strlen($notFoundSuffix);
        if ($notFoundSuffixLen > $messageLen) {
            return null;
        }

        if (0 !== substr_compare($message, $notFoundSuffix, -$notFoundSuffixLen)) {
            return null;
        }

        $prefix = 'Call to undefined function ';
        $prefixLen = \strlen($prefix);
        if (!str_starts_with($message, $prefix)) {
            return null;
        }

        $fullyQualifiedFunctionName = substr($message, $prefixLen, -$notFoundSuffixLen);
        if (false !== $namespaceSeparatorIndex = strrpos($fullyQualifiedFunctionName, '\\')) {
            $functionName = substr($fullyQualifiedFunctionName, $namespaceSeparatorIndex + 1);
            $namespacePrefix = substr($fullyQualifiedFunctionName, 0, $namespaceSeparatorIndex);
            $message = \sprintf('Attempted to call undefined function "%s" from namespace "%s".', $functionName, $namespacePrefix);
        } else {
            $functionName = $fullyQualifiedFunctionName;
            $message = \sprintf('Attempted to call undefined function "%s" from the global namespace.', $functionName);
        }

        $candidates = [];
        foreach (get_defined_functions() as $type => $definedFunctionNames) {
            foreach ($definedFunctionNames as $definedFunctionName) {
                if (false !== $namespaceSeparatorIndex = strrpos($definedFunctionName, '\\')) {
                    $definedFunctionNameBasename = substr($definedFunctionName, $namespaceSeparatorIndex + 1);
                } else {
                    $definedFunctionNameBasename = $definedFunctionName;
                }

                if ($definedFunctionNameBasename === $functionName) {
                    $candidates[] = '\\'.$definedFunctionName;
                }
            }
        }

        if ($candidates) {
            sort($candidates);
            $last = array_pop($candidates).'"?';
            if ($candidates) {
                $candidates = 'e.g. "'.implode('", "', $candidates).'" or "'.$last;
            } else {
                $candidates = '"'.$last;
            }
            $message .= "\nDid you mean to call ".$candidates;
        }

        return new UndefinedFunctionError($message, $error);
    }
}
