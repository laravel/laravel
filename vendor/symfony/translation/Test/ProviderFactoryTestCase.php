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

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * A test case to ease testing a translation provider factory.
 *
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 *
 * @deprecated since Symfony 7.2, use AbstractProviderFactoryTestCase instead
 */
abstract class ProviderFactoryTestCase extends AbstractProviderFactoryTestCase
{
    use IncompleteDsnTestTrait;

    protected HttpClientInterface $client;
    protected LoggerInterface|MockObject $logger;
    protected string $defaultLocale;
    protected LoaderInterface|MockObject $loader;
    protected XliffFileDumper|MockObject $xliffFileDumper;
    protected TranslatorBagInterface|MockObject $translatorBag;

    /**
     * @return iterable<array{0: string, 1?: string|null}>
     */
    public static function unsupportedSchemeProvider(): iterable
    {
        return [];
    }

    /**
     * @return iterable<array{0: string, 1?: string|null}>
     */
    public static function incompleteDsnProvider(): iterable
    {
        return [];
    }

    protected function getClient(): HttpClientInterface
    {
        return $this->client ??= new MockHttpClient();
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger ??= $this->createMock(LoggerInterface::class);
    }

    protected function getDefaultLocale(): string
    {
        return $this->defaultLocale ??= 'en';
    }

    protected function getLoader(): LoaderInterface
    {
        return $this->loader ??= $this->createMock(LoaderInterface::class);
    }

    protected function getXliffFileDumper(): XliffFileDumper
    {
        return $this->xliffFileDumper ??= $this->createMock(XliffFileDumper::class);
    }

    protected function getTranslatorBag(): TranslatorBagInterface
    {
        return $this->translatorBag ??= $this->createMock(TranslatorBagInterface::class);
    }
}
