<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Attribute;

use Symfony\Component\Console\Attribute\Reflection\ReflectionMember;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::TARGET_PROPERTY)]
class Ask implements InteractiveAttributeInterface
{
    public ?\Closure $normalizer;
    public ?\Closure $validator;
    private \Closure $closure;

    /**
     * @param string                     $question    The question to ask the user
     * @param string|bool|int|float|null $default     The default answer to return if the user enters nothing
     * @param bool                       $hidden      Whether the user response must be hidden or not
     * @param bool                       $multiline   Whether the user response should accept newline characters
     * @param bool                       $trimmable   Whether the user response must be trimmed or not
     * @param int|null                   $timeout     The maximum time the user has to answer the question in seconds
     * @param callable|null              $validator   The validator for the question
     * @param int|null                   $maxAttempts The maximum number of attempts allowed to answer the question.
     *                                                Null means an unlimited number of attempts
     */
    public function __construct(
        public string $question,
        public string|bool|int|float|null $default = null,
        public bool $hidden = false,
        public bool $multiline = false,
        public bool $trimmable = true,
        public ?int $timeout = null,
        ?callable $normalizer = null,
        ?callable $validator = null,
        public ?int $maxAttempts = null,
    ) {
        $this->normalizer = $normalizer ? $normalizer(...) : null;
        $this->validator = $validator ? $validator(...) : null;
    }

    /**
     * @internal
     */
    public static function tryFrom(\ReflectionParameter|\ReflectionProperty $member, string $name): ?self
    {
        $reflection = new ReflectionMember($member);

        if (!$self = $reflection->getAttribute(self::class)) {
            return null;
        }

        $type = $reflection->getType();

        if (!$type instanceof \ReflectionNamedType) {
            throw new LogicException(\sprintf('The %s "$%s" of "%s" must have a named type. Untyped, Union or Intersection types are not supported for interactive questions.', $reflection->getMemberName(), $name, $reflection->getSourceName()));
        }

        $self->closure = function (SymfonyStyle $io, InputInterface $input) use ($self, $reflection, $name, $type) {
            if ($reflection->isProperty() && isset($this->{$reflection->getName()})) {
                return;
            }

            if ($reflection->isParameter() && !\in_array($input->getArgument($name), [null, []], true)) {
                return;
            }

            if ('bool' === $type->getName()) {
                $self->default ??= false;

                if (!\is_bool($self->default)) {
                    throw new LogicException(\sprintf('The "%s::$default" value for the %s "$%s" of "%s" must be a boolean.', self::class, $reflection->getMemberName(), $name, $reflection->getSourceName()));
                }

                $question = new ConfirmationQuestion($self->question, $self->default);
            } else {
                $question = new Question($self->question, $self->default);
            }
            $question->setHidden($self->hidden);
            $question->setMultiline($self->multiline);
            $question->setTrimmable($self->trimmable);
            $question->setTimeout($self->timeout);

            if (!$self->validator && $reflection->isProperty() && 'array' !== $type->getName()) {
                $self->validator = fn (mixed $value): mixed => $this->{$reflection->getName()} = $value;
            }

            $question->setValidator($self->validator);
            $question->setMaxAttempts($self->maxAttempts);

            if ($self->normalizer) {
                $question->setNormalizer($self->normalizer);
            } elseif (is_subclass_of($type->getName(), \BackedEnum::class)) {
                /** @var class-string<\BackedEnum> $backedType */
                $backedType = $reflection->getType()->getName();
                $question->setNormalizer(static fn (string|int $value) => $backedType::tryFrom($value) ?? throw InvalidArgumentException::fromEnumValue($reflection->getName(), $value, array_column($backedType::cases(), 'value')));
            }

            if ('array' === $type->getName()) {
                $value = [];
                while ($v = $io->askQuestion($question)) {
                    if ("\x4" === $v || \PHP_EOL === $v || ($question->isTrimmable() && '' === $v = trim($v))) {
                        break;
                    }
                    $value[] = $v;
                }
            } else {
                $value = $io->askQuestion($question);
            }

            if (null === $value && !$reflection->isNullable()) {
                return;
            }

            if ($reflection->isProperty()) {
                $this->{$reflection->getName()} = $value;
            } else {
                $input->setArgument($name, $value);
            }
        };

        return $self;
    }

    /**
     * @internal
     */
    public function getFunction(object $instance): \ReflectionFunction
    {
        return new \ReflectionFunction($this->closure->bindTo($instance, $instance::class));
    }
}
