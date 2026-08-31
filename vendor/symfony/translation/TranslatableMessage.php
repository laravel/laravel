<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Nate Wiebe <nate@northern.co>
 */
class TranslatableMessage implements TranslatableInterface
{
    public function __construct(
        private string $message,
        private array $parameters = [],
        private ?string $domain = null,
    ) {
    }

    /**
     * @deprecated since Symfony 7.4
     */
    public function __toString(): string
    {
        trigger_deprecation('symfony/translation', '7.4', 'Method "%s()" is deprecated.', __METHOD__);

        return $this->getMessage();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        $parameters = $this->getParameters();
        foreach ($parameters as $k => $v) {
            if ($v instanceof TranslatableInterface) {
                $parameters[$k] = $v->trans($translator, $locale);
            }
        }

        return $translator->trans($this->getMessage(), $parameters, $this->getDomain(), $locale);
    }
}
