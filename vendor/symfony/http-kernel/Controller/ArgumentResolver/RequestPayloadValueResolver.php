<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NearMissValueResolverException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\InvalidArgumentException as SerializerInvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Exception\UnexpectedPropertyException;
use Symfony\Component\Serializer\Exception\UnsupportedFormatException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 *
 * @final
 */
class RequestPayloadValueResolver implements ValueResolverInterface, EventSubscriberInterface
{
    /**
     * @see DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS
     */
    private const CONTEXT_DENORMALIZE = [
        'collect_denormalization_errors' => true,
    ];

    /**
     * @see DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS
     */
    private const CONTEXT_DESERIALIZE = [
        'collect_denormalization_errors' => true,
    ];

    public function __construct(
        private readonly SerializerInterface&DenormalizerInterface $serializer,
        private readonly ?ValidatorInterface $validator = null,
        private readonly ?TranslatorInterface $translator = null,
        private string $translationDomain = 'validators',
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute = $argument->getAttributesOfType(MapQueryString::class, ArgumentMetadata::IS_INSTANCEOF)[0]
            ?? $argument->getAttributesOfType(MapRequestPayload::class, ArgumentMetadata::IS_INSTANCEOF)[0]
            ?? $argument->getAttributesOfType(MapUploadedFile::class, ArgumentMetadata::IS_INSTANCEOF)[0]
            ?? null;

        if (!$attribute) {
            return [];
        }

        if (!$attribute instanceof MapUploadedFile && $argument->isVariadic()) {
            throw new \LogicException(\sprintf('Mapping variadic argument "$%s" is not supported.', $argument->getName()));
        }

        if ($attribute instanceof MapRequestPayload) {
            if ('array' === $argument->getType()) {
                if (!$attribute->type) {
                    throw new NearMissValueResolverException(\sprintf('Please set the $type argument of the #[%s] attribute to the type of the objects in the expected array.', MapRequestPayload::class));
                }
            } elseif ($attribute->type) {
                throw new NearMissValueResolverException(\sprintf('Please set its type to "array" when using argument $type of #[%s].', MapRequestPayload::class));
            }
        }

        $attribute->metadata = $argument;

        return [$attribute];
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $arguments = $event->getArguments();

        foreach ($arguments as $i => $argument) {
            if ($argument instanceof MapQueryString) {
                $payloadMapper = $this->mapQueryString(...);
                $validationFailedCode = $argument->validationFailedStatusCode;
            } elseif ($argument instanceof MapRequestPayload) {
                $payloadMapper = $this->mapRequestPayload(...);
                $validationFailedCode = $argument->validationFailedStatusCode;
            } elseif ($argument instanceof MapUploadedFile) {
                $payloadMapper = $this->mapUploadedFile(...);
                $validationFailedCode = $argument->validationFailedStatusCode;
            } else {
                continue;
            }
            $request = $event->getRequest();

            if (!$argument->metadata->getType()) {
                throw new \LogicException(\sprintf('Could not resolve the "$%s" controller argument: argument should be typed.', $argument->metadata->getName()));
            }

            if ($this->validator) {
                $violations = new ConstraintViolationList();
                try {
                    $payload = $payloadMapper($request, $argument->metadata, $argument);
                } catch (PartialDenormalizationException $e) {
                    $trans = $this->translator ? $this->translator->trans(...) : static fn ($m, $p) => strtr($m, $p);
                    foreach ($e->getErrors() as $error) {
                        $parameters = [];
                        $template = 'This value was of an unexpected type.';
                        if ($expectedTypes = $error->getExpectedTypes()) {
                            $template = 'This value should be of type {{ type }}.';
                            $parameters['{{ type }}'] = implode('|', $expectedTypes);
                        }
                        if ($error->canUseMessageForUser()) {
                            $parameters['hint'] = $error->getMessage();
                        }
                        $message = $trans($template, $parameters, $this->translationDomain);
                        $violations->add(new ConstraintViolation($message, $template, $parameters, null, $error->getPath(), null));
                    }
                    $payload = $e->getData();
                } catch (SerializerInvalidArgumentException $e) {
                    $violations->add(new ConstraintViolation($e->getMessage(), $e->getMessage(), [], null, '', null));
                    $payload = null;
                }

                if (null !== $payload && !\count($violations)) {
                    $constraints = $argument->constraints ?? null;
                    if (\is_array($payload) && !empty($constraints) && !$constraints instanceof Assert\All) {
                        $constraints = new Assert\All($constraints);
                    }
                    if ($argument instanceof MapUploadedFile) {
                        $violations->addAll($this->validator->startContext()->atPath($argument->metadata->getName())->validate($payload, $constraints, $argument->validationGroups ?? null)->getViolations());
                    } else {
                        $violations->addAll($this->validator->validate($payload, $constraints, $argument->validationGroups ?? null));
                    }
                }

                if (\count($violations)) {
                    throw HttpException::fromStatusCode($validationFailedCode, implode("\n", array_map(static fn ($e) => $e->getMessage(), iterator_to_array($violations))), new ValidationFailedException($payload, $violations));
                }
            } else {
                try {
                    $payload = $payloadMapper($request, $argument->metadata, $argument);
                } catch (PartialDenormalizationException $e) {
                    throw HttpException::fromStatusCode($validationFailedCode, implode("\n", array_map(static fn ($e) => $e->getMessage(), $e->getErrors())), $e);
                } catch (SerializerInvalidArgumentException $e) {
                    throw HttpException::fromStatusCode($validationFailedCode, $e->getMessage(), $e);
                }
            }

            if ($argument->metadata->isVariadic()) {
                array_splice($arguments, $i, 1, $payload ?? []);
                continue;
            }

            if (null === $payload) {
                $payload = match (true) {
                    $argument->metadata->hasDefaultValue() => $argument->metadata->getDefaultValue(),
                    $argument->metadata->isNullable() => null,
                    default => throw HttpException::fromStatusCode($validationFailedCode),
                };
            }

            $arguments[$i] = $payload;
        }

        $event->setArguments($arguments);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }

    private function mapQueryString(Request $request, ArgumentMetadata $argument, MapQueryString $attribute): ?object
    {
        if (!($data = $request->query->all($attribute->key)) && ($argument->isNullable() || $argument->hasDefaultValue())) {
            return null;
        }

        return $this->serializer->denormalize($data, $argument->getType(), 'csv', $attribute->serializationContext + self::CONTEXT_DENORMALIZE + ['filter_bool' => true]);
    }

    private function mapRequestPayload(Request $request, ArgumentMetadata $argument, MapRequestPayload $attribute): object|array|null
    {
        if ('' === ($data = $request->request->all() ?: $request->getContent()) && ($argument->isNullable() || $argument->hasDefaultValue())) {
            return null;
        }

        if (null === $format = $request->getContentTypeFormat()) {
            throw new UnsupportedMediaTypeHttpException('Unsupported format.');
        }

        if ($attribute->acceptFormat && !\in_array($format, (array) $attribute->acceptFormat, true)) {
            throw new UnsupportedMediaTypeHttpException(\sprintf('Unsupported format, expects "%s", but "%s" given.', implode('", "', (array) $attribute->acceptFormat), $format));
        }

        if ('array' === $argument->getType() && null !== $attribute->type) {
            $type = $attribute->type.'[]';
        } else {
            $type = $argument->getType();
        }

        if (\is_array($data)) {
            return $this->serializer->denormalize($data, $type, self::hasNonStringScalar($data) ? $format : 'csv', $attribute->serializationContext + self::CONTEXT_DENORMALIZE + ('form' === $format ? ['filter_bool' => true] : []));
        }

        if ('form' === $format) {
            throw new BadRequestHttpException('Request payload contains invalid "form" data.');
        }

        try {
            return $this->serializer->deserialize($data, $type, $format, self::CONTEXT_DESERIALIZE + $attribute->serializationContext);
        } catch (UnsupportedFormatException $e) {
            throw new UnsupportedMediaTypeHttpException(\sprintf('Unsupported format: "%s".', $format), $e);
        } catch (NotEncodableValueException $e) {
            throw new BadRequestHttpException(\sprintf('Request payload contains invalid "%s" data.', $format), $e);
        } catch (UnexpectedPropertyException $e) {
            throw new BadRequestHttpException(\sprintf('Request payload contains invalid "%s" property.', $e->property), $e);
        }
    }

    private function mapUploadedFile(Request $request, ArgumentMetadata $argument, MapUploadedFile $attribute): UploadedFile|array|null
    {
        if ($files = $request->files->get($attribute->name ?? $argument->getName())) {
            return !\is_array($files) && $argument->isVariadic() ? [$files] : $files;
        }

        if ($argument->isNullable() || $argument->hasDefaultValue()) {
            return null;
        }

        return 'array' === $argument->getType() ? [] : null;
    }

    private static function hasNonStringScalar(array $data): bool
    {
        $stack = [$data];

        while ($stack) {
            foreach (array_pop($stack) as $v) {
                if (\is_array($v)) {
                    $stack[] = $v;
                } elseif (!\is_string($v)) {
                    return true;
                }
            }
        }

        return false;
    }
}
