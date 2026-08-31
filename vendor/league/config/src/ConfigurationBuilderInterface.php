<?php

declare(strict_types=1);

/*
 * This file is part of the league/config package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Config;

/**
 * An interface that provides the ability to set both the schema and configuration values
 */
interface ConfigurationBuilderInterface extends MutableConfigurationInterface, SchemaBuilderInterface
{
}
