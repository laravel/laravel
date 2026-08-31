<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Provider\ProviderInterface;
use Symfony\Component\Translation\TranslatorBag;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * A test case to ease testing a translation provider.
 *
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 */
abstract class ProviderTestCase extends TestCase
{
    protected HttpClientInterface $client;
    protected LoggerInterface|MockObject $logger;
    protected string $defaultLocale;
    protected LoaderInterface|MockObject $loader;
    protected XliffFileDumper|MockObject $xliffFileDumper;
    protected TranslatorBagInterface|MockObject $translatorBag;

    abstract public static function createProvider(HttpClientInterface $client, LoaderInterface $loader, LoggerInterface $logger, string $defaultLocale, string $endpoint): ProviderInterface;

    /**
     * @return iterable<array{0: ProviderInterface, 1: string}>
     */
    abstract public static function toStringProvider(): iterable;

    /**
     * @dataProvider toStringProvider
     */
    #[DataProvider('toStringProvider')]
    public function testToString(ProviderInterface $provider, string $expected)
    {
        $this->assertSame($expected, (string) $provider);
    }

    protected function getClient(): MockHttpClient
    {
        return $this->client ??= new MockHttpClient();
    }

    protected function getLoader(): LoaderInterface
    {
        return $this->loader ??= new ArrayLoader();
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger ??= new NullLogger();
    }

    protected function getDefaultLocale(): string
    {
        return $this->defaultLocale ??= 'en';
    }

    protected function getXliffFileDumper(): XliffFileDumper
    {
        return $this->xliffFileDumper ??= new XliffFileDumper();
    }

    protected function getTranslatorBag(): TranslatorBagInterface
    {
        return $this->translatorBag ??= new TranslatorBag();
    }
}
