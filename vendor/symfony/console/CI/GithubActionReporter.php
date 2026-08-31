<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\CI;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Utility class for Github actions.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class GithubActionReporter
{
    /**
     * @see https://github.com/actions/toolkit/blob/5e5e1b7aacba68a53836a34db4a288c3c1c1585b/packages/core/src/command.ts#L80-L85
     */
    private const ESCAPED_DATA = [
        '%' => '%25',
        "\r" => '%0D',
        "\n" => '%0A',
    ];

    /**
     * @see https://github.com/actions/toolkit/blob/5e5e1b7aacba68a53836a34db4a288c3c1c1585b/packages/core/src/command.ts#L87-L94
     */
    private const ESCAPED_PROPERTIES = [
        '%' => '%25',
        "\r" => '%0D',
        "\n" => '%0A',
        ':' => '%3A',
        ',' => '%2C',
    ];

    public function __construct(
        private OutputInterface $output,
    ) {
    }

    public static function isGithubActionEnvironment(): bool
    {
        return false !== getenv('GITHUB_ACTIONS');
    }

    /**
     * Output an error using the Github annotations format.
     *
     * @see https://docs.github.com/en/free-pro-team@latest/actions/reference/workflow-commands-for-github-actions#setting-an-error-message
     */
    public function error(string $message, ?string $file = null, ?int $line = null, ?int $col = null): void
    {
        $this->log('error', $message, $file, $line, $col);
    }

    /**
     * Output a warning using the Github annotations format.
     *
     * @see https://docs.github.com/en/free-pro-team@latest/actions/reference/workflow-commands-for-github-actions#setting-a-warning-message
     */
    public function warning(string $message, ?string $file = null, ?int $line = null, ?int $col = null): void
    {
        $this->log('warning', $message, $file, $line, $col);
    }

    /**
     * Output a debug log using the Github annotations format.
     *
     * @see https://docs.github.com/en/free-pro-team@latest/actions/reference/workflow-commands-for-github-actions#setting-a-debug-message
     */
    public function debug(string $message, ?string $file = null, ?int $line = null, ?int $col = null): void
    {
        $this->log('debug', $message, $file, $line, $col);
    }

    private function log(string $type, string $message, ?string $file = null, ?int $line = null, ?int $col = null): void
    {
        // Some values must be encoded.
        $message = strtr($message, self::ESCAPED_DATA);

        if (!$file) {
            // No file provided, output the message solely:
            $this->output->writeln(\sprintf('::%s::%s', $type, $message));

            return;
        }

        $this->output->writeln(\sprintf('::%s file=%s,line=%s,col=%s::%s', $type, strtr($file, self::ESCAPED_PROPERTIES), strtr($line ?? 1, self::ESCAPED_PROPERTIES), strtr($col ?? 0, self::ESCAPED_PROPERTIES), $message));
    }
}
