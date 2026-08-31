<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler\FingersCrossed;

use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Monolog\LogRecord;

/**
 * Channel and Error level based monolog activation strategy. Allows to trigger activation
 * based on level per channel. e.g. trigger activation on level 'ERROR' by default, except
 * for records of the 'sql' channel; those should trigger activation on level 'WARN'.
 *
 * Example:
 *
 * <code>
 *   $activationStrategy = new ChannelLevelActivationStrategy(
 *       Level::Critical,
 *       array(
 *           'request' => Level::Alert,
 *           'sensitive' => Level::Error,
 *       )
 *   );
 *   $handler = new FingersCrossedHandler(new StreamHandler('php://stderr'), $activationStrategy);
 * </code>
 *
 * @author Mike Meessen <netmikey@gmail.com>
 */
class ChannelLevelActivationStrategy implements ActivationStrategyInterface
{
    private Level $defaultActionLevel;

    /**
     * @var array<string, Level>
     */
    private array $channelToActionLevel;

    /**
     * @param int|string|Level|LogLevel::*                $defaultActionLevel   The default action level to be used if the record's category doesn't match any
     * @param array<string, int|string|Level|LogLevel::*> $channelToActionLevel An array that maps channel names to action levels.
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $defaultActionLevel
     * @phpstan-param array<string, value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::*> $channelToActionLevel
     */
    public function __construct(int|string|Level $defaultActionLevel, array $channelToActionLevel = [])
    {
        $this->defaultActionLevel = Logger::toMonologLevel($defaultActionLevel);
        $this->channelToActionLevel = array_map(Logger::toMonologLevel(...), $channelToActionLevel);
    }

    public function isHandlerActivated(LogRecord $record): bool
    {
        if (isset($this->channelToActionLevel[$record->channel])) {
            return $record->level->value >= $this->channelToActionLevel[$record->channel]->value;
        }

        return $record->level->value >= $this->defaultActionLevel->value;
    }
}
