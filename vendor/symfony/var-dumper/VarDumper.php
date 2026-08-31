<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper;

use Symfony\Component\ErrorHandler\ErrorRenderer\FileLinkFormatter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\VarDumper\Caster\ReflectionCaster;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\RequestContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextualizedDumper;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\ServerDumper;

// Load the global dump() function
require_once __DIR__.'/Resources/functions/dump.php';

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class VarDumper
{
    /**
     * @var callable|null
     */
    private static $handler;

    public static function dump(mixed $var, ?string $label = null): mixed
    {
        if (null === self::$handler) {
            self::register();
        }

        return (self::$handler)($var, $label);
    }

    public static function setHandler(?callable $callable): ?callable
    {
        $prevHandler = self::$handler;

        // Prevent replacing the handler with expected format as soon as the env var was set:
        if (isset($_SERVER['VAR_DUMPER_FORMAT'])) {
            return $prevHandler;
        }

        self::$handler = $callable;

        return $prevHandler;
    }

    private static function register(): void
    {
        $cloner = new VarCloner();
        $cloner->addCasters(ReflectionCaster::UNSET_CLOSURE_FILE_INFO);

        $format = $_SERVER['VAR_DUMPER_FORMAT'] ?? null;

        $dumper = match ($format) {
            'html' => new HtmlDumper(),
            'cli' => new CliDumper(),
            'server' => self::selectDumperForAccept($_SERVER['VAR_DUMPER_SERVER'] ?? '127.0.0.1:9912'),
            default => self::selectDumperForAccept(
                $format && 'tcp' === parse_url($format, \PHP_URL_SCHEME) ? $format : null,
            ),
        };

        if (!$dumper instanceof ServerDumper) {
            $dumper = new ContextualizedDumper($dumper, [new SourceContextProvider()]);
        }

        self::$handler = static function ($var, ?string $label = null) use ($cloner, $dumper) {
            $var = $cloner->cloneVar($var);

            if (null !== $label) {
                $var = $var->withContext(['label' => $label]);
            }

            $dumper->dump($var);
        };
    }

    private static function selectDumperForAccept(?string $serverHost): DataDumperInterface
    {
        $isCliSapi = \in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true);
        $accept = $_SERVER['HTTP_ACCEPT'] ?? ($isCliSapi ? 'txt' : 'html');

        $dumper = match (true) {
            str_contains($accept, 'html'), str_contains($accept, '*/*') => new HtmlDumper(),
            $isCliSapi => new CliDumper(),
            default => new CliDumper('php://output'),
        };

        if (null !== $serverHost) {
            $dumper = new ServerDumper($serverHost, $dumper, self::getDefaultContextProviders());
        }

        return $dumper;
    }

    private static function getDefaultContextProviders(): array
    {
        $contextProviders = [];

        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && class_exists(Request::class)) {
            $requestStack = new RequestStack();
            $requestStack->push(Request::createFromGlobals());
            $contextProviders['request'] = new RequestContextProvider($requestStack);
        }

        $fileLinkFormatter = class_exists(FileLinkFormatter::class) ? new FileLinkFormatter(null, $requestStack ?? null) : null;

        return $contextProviders + [
            'cli' => new CliContextProvider(),
            'source' => new SourceContextProvider(null, null, $fileLinkFormatter),
        ];
    }
}
