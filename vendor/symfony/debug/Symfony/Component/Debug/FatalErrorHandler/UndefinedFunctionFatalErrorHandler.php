<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\FatalErrorHandler;

use Symfony\Component\Debug\Exception\UndefinedFunctionException;
use Symfony\Component\Debug\Exception\FatalErrorException;

/**
 * ErrorHandler for undefined functions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UndefinedFunctionFatalErrorHandler implements FatalErrorHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handleError(array $error, FatalErrorException $exception)
    {
        $messageLen = strlen($error['message']);
        $notFoundSuffix = '()';
        $notFoundSuffixLen = strlen($notFoundSuffix);
        if ($notFoundSuffixLen > $messageLen) {
            return;
        }

        if (0 !== substr_compare($error['message'], $notFoundSuffix, -$notFoundSuffixLen)) {
            return;
        }

        $prefix = 'Call to undefined function ';
        $prefixLen = strlen($prefix);
        if (0 !== strpos($error['message'], $prefix)) {
            return;
        }

        $fullyQualifiedFunctionName = substr($error['message'], $prefixLen, -$notFoundSuffixLen);
        if (false !== $namespaceSeparatorIndex = strrpos($fullyQualifiedFunctionName, '\\')) {
            $functionName = substr($fullyQualifiedFunctionName, $namespaceSeparatorIndex + 1);
            $namespacePrefix = substr($fullyQualifiedFunctionName, 0, $namespaceSeparatorIndex);
            $message = sprintf(
                'Attempted to call function "%s" from namespace "%s" in %s line %d.',
                $functionName,
                $namespacePrefix,
                $error['file'],
                $error['line']
            );
        } else {
            $functionName = $fullyQualifiedFunctionName;
            $message = sprintf(
                'Attempted to call function "%s" from the global namespace in %s line %d.',
                $functionName,
                $error['file'],
                $error['line']
            );
        }

        $candidates = array();
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
            $message .= ' Did you mean to call: '.implode(', ', array_map(function ($val) {
                return '"'.$val.'"';
            }, $candidates)).'?';
        }

        return new UndefinedFunctionException($message, $exception);
    }
}
