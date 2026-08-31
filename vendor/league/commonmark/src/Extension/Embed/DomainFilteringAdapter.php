<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Embed;

class DomainFilteringAdapter implements EmbedAdapterInterface
{
    private EmbedAdapterInterface $decorated;

    /** @var string[] */
    private array $allowedDomains;

    /**
     * @param string[] $allowedDomains
     */
    public function __construct(EmbedAdapterInterface $decorated, array $allowedDomains)
    {
        $this->decorated      = $decorated;
        $this->allowedDomains = \array_map('strtolower', $allowedDomains);
    }

    /**
     * {@inheritDoc}
     */
    public function updateEmbeds(array $embeds): void
    {
        $this->decorated->updateEmbeds(\array_values(\array_filter($embeds, [$this, 'isAllowed'])));
    }

    private function isAllowed(Embed $embed): bool
    {
        $url    = $embed->getUrl();
        $scheme = \parse_url($url, \PHP_URL_SCHEME);
        if ($scheme === null || $scheme === false) {
            // Bare domain (no scheme) - assume https:// so parse_url can extract the host
            $url = 'https://' . $url;
        } elseif (\strtolower($scheme) !== 'http' && \strtolower($scheme) !== 'https') {
            return false;
        }

        $host = \parse_url($url, \PHP_URL_HOST);
        $host = \strtolower(\rtrim((string) $host, '.'));

        foreach ($this->allowedDomains as $domain) {
            if ($host === $domain || \str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }
}
