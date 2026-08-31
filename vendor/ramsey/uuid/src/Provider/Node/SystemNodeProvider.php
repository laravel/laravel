<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Exception\NodeException;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;

use function array_filter;
use function array_map;
use function array_walk;
use function count;
use function ob_get_clean;
use function ob_start;
use function preg_match;
use function preg_match_all;
use function reset;
use function str_contains;
use function str_replace;
use function strtolower;
use function strtoupper;
use function substr;

use const GLOB_NOSORT;
use const PREG_PATTERN_ORDER;

/**
 * SystemNodeProvider retrieves the system node ID, if possible
 *
 * The system node ID, or host ID, is often the same as the MAC address for a network interface on the host.
 */
class SystemNodeProvider implements NodeProviderInterface
{
    /**
     * Pattern to match nodes in `ifconfig` and `ipconfig` output.
     */
    private const IFCONFIG_PATTERN = '/[^:]([0-9a-f]{2}([:-])[0-9a-f]{2}(\2[0-9a-f]{2}){4})[^:]/i';

    /**
     * Pattern to match nodes in sysfs stream output.
     */
    private const SYSFS_PATTERN = '/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/i';

    public function getNode(): Hexadecimal
    {
        $node = $this->getNodeFromSystem();

        if ($node === '') {
            throw new NodeException('Unable to fetch a node for this system');
        }

        return new Hexadecimal($node);
    }

    /**
     * Returns the system node if found
     */
    protected function getNodeFromSystem(): string
    {
        /** @var string | null $node */
        static $node = null;

        if ($node !== null) {
            return $node;
        }

        // First, try a Linux-specific approach.
        $node = $this->getSysfs();

        if ($node === '') {
            // Search ifconfig output for MAC addresses & return the first one.
            $node = $this->getIfconfig();
        }

        $node = str_replace([':', '-'], '', $node);

        return $node;
    }

    /**
     * Returns the network interface configuration for the system
     *
     * @codeCoverageIgnore
     */
    protected function getIfconfig(): string
    {
        if (str_contains(strtolower((string) ini_get('disable_functions')), 'passthru')) {
            return '';
        }

        /** @var string $phpOs */
        $phpOs = constant('PHP_OS');

        ob_start();
        switch (strtoupper(substr($phpOs, 0, 3))) {
            case 'WIN':
                passthru('ipconfig /all 2>&1');

                break;
            case 'DAR':
                passthru('ifconfig 2>&1');

                break;
            case 'FRE':
                passthru('netstat -i -f link 2>&1');

                break;
            case 'LIN':
            default:
                passthru('netstat -ie 2>&1');

                break;
        }

        $ifconfig = (string) ob_get_clean();

        if (preg_match_all(self::IFCONFIG_PATTERN, $ifconfig, $matches, PREG_PATTERN_ORDER)) {
            foreach ($matches[1] as $iface) {
                if ($iface !== '00:00:00:00:00:00' && $iface !== '00-00-00-00-00-00') {
                    return $iface;
                }
            }
        }

        return '';
    }

    /**
     * Returns MAC address from the first system interface via the sysfs interface
     */
    protected function getSysfs(): string
    {
        /** @var string $phpOs */
        $phpOs = constant('PHP_OS');

        if (strtoupper($phpOs) !== 'LINUX') {
            return '';
        }

        $addressPaths = glob('/sys/class/net/*/address', GLOB_NOSORT);

        if ($addressPaths === false || count($addressPaths) === 0) {
            return '';
        }

        /** @var array<array-key, string> $macs */
        $macs = [];

        array_walk($addressPaths, function (string $addressPath) use (&$macs): void {
            if (is_readable($addressPath)) {
                $macs[] = file_get_contents($addressPath);
            }
        });

        /** @var callable $trim */
        $trim = 'trim';

        $macs = array_map($trim, $macs);

        // Remove invalid entries.
        $macs = array_filter($macs, function (mixed $address): bool {
            assert(is_string($address));

            return $address !== '00:00:00:00:00:00' && preg_match(self::SYSFS_PATTERN, $address);
        });

        /** @var bool | string $mac */
        $mac = reset($macs);

        return (string) $mac;
    }
}
