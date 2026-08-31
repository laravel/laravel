<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Extractor\Visitor;

use PhpParser\Node;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 */
abstract class AbstractVisitor
{
    private MessageCatalogue $catalogue;
    private \SplFileInfo $file;
    private string $messagePrefix;

    public function initialize(MessageCatalogue $catalogue, \SplFileInfo $file, string $messagePrefix): void
    {
        $this->catalogue = $catalogue;
        $this->file = $file;
        $this->messagePrefix = $messagePrefix;
    }

    protected function addMessageToCatalogue(string $message, ?string $domain, int $line): void
    {
        $domain ??= 'messages';
        $this->catalogue->set($message, $this->messagePrefix.$message, $domain);
        $metadata = $this->catalogue->getMetadata($message, $domain) ?? [];
        $normalizedFilename = preg_replace('{[\\\\/]+}', '/', $this->file);
        $metadata['sources'][] = $normalizedFilename.':'.$line;
        $this->catalogue->setMetadata($message, $metadata, $domain);
    }

    protected function getStringArguments(Node\Expr\CallLike|Node\Attribute|Node\Expr\New_ $node, int|string $index, bool $indexIsRegex = false): array
    {
        if (\is_string($index)) {
            return $this->getStringNamedArguments($node, $index, $indexIsRegex);
        }

        $args = $node instanceof Node\Expr\CallLike ? $node->getRawArgs() : $node->args;

        if (!($arg = $args[$index] ?? null) instanceof Node\Arg) {
            return [];
        }

        return (array) $this->getStringValue($arg->value);
    }

    protected function hasNodeNamedArguments(Node\Expr\CallLike|Node\Attribute|Node\Expr\New_ $node): bool
    {
        $args = $node instanceof Node\Expr\CallLike ? $node->getRawArgs() : $node->args;

        foreach ($args as $arg) {
            if ($arg instanceof Node\Arg && null !== $arg->name) {
                return true;
            }
        }

        return false;
    }

    protected function nodeFirstNamedArgumentIndex(Node\Expr\CallLike|Node\Attribute|Node\Expr\New_ $node): int
    {
        $args = $node instanceof Node\Expr\CallLike ? $node->getRawArgs() : $node->args;

        foreach ($args as $i => $arg) {
            if ($arg instanceof Node\Arg && null !== $arg->name) {
                return $i;
            }
        }

        return \PHP_INT_MAX;
    }

    private function getStringNamedArguments(Node\Expr\CallLike|Node\Attribute $node, ?string $argumentName = null, bool $isArgumentNamePattern = false): array
    {
        $args = $node instanceof Node\Expr\CallLike ? $node->getArgs() : $node->args;
        $argumentValues = [];

        foreach ($args as $arg) {
            if (!$isArgumentNamePattern && $arg->name?->toString() === $argumentName) {
                $argumentValues[] = $this->getStringValue($arg->value);
            } elseif ($isArgumentNamePattern && preg_match($argumentName, $arg->name?->toString() ?? '') > 0) {
                $argumentValues[] = $this->getStringValue($arg->value);
            }
        }

        return array_filter($argumentValues);
    }

    private function getStringValue(Node $node): ?string
    {
        if ($node instanceof Node\Scalar\String_) {
            return $node->value;
        }

        if ($node instanceof Node\Expr\BinaryOp\Concat) {
            if (null === $left = $this->getStringValue($node->left)) {
                return null;
            }

            if (null === $right = $this->getStringValue($node->right)) {
                return null;
            }

            return $left.$right;
        }

        if ($node instanceof Node\Expr\Assign && $node->expr instanceof Node\Scalar\String_) {
            return $node->expr->value;
        }

        if ($node instanceof Node\Expr\ClassConstFetch) {
            try {
                $reflection = new \ReflectionClass($node->class->toString());
                $constant = $reflection->getReflectionConstant($node->name->toString());
                if (false !== $constant && \is_string($constant->getValue())) {
                    return $constant->getValue();
                }
            } catch (\ReflectionException) {
            }
        }

        return null;
    }
}
