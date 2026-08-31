<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Attribute;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints\GroupSequence;

/**
 * Controller parameter tag to map the request content to typed object and validate it.
 *
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class MapRequestPayload extends ValueResolver
{
    public ArgumentMetadata $metadata;

    /**
     * @param array<string>|string|null               $acceptFormat               The payload formats to accept (i.e. "json", "xml")
     * @param array<string, mixed>                    $serializationContext       The serialization context to use when deserializing the payload
     * @param string|GroupSequence|array<string>|null $validationGroups           The validation groups to use when validating the query string mapping
     * @param class-string                            $resolver                   The class name of the resolver to use
     * @param int                                     $validationFailedStatusCode The HTTP code to return if the validation fails
     * @param class-string|string|null                $type                       The element type for array deserialization
     */
    public function __construct(
        public readonly array|string|null $acceptFormat = null,
        public readonly array $serializationContext = [],
        public readonly string|GroupSequence|array|null $validationGroups = null,
        string $resolver = RequestPayloadValueResolver::class,
        public readonly int $validationFailedStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY,
        public readonly ?string $type = null,
    ) {
        parent::__construct($resolver);
    }
}
