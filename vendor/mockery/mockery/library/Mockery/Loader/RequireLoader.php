<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

use function array_diff;
use function class_exists;
use function file_exists;
use function file_put_contents;
use function glob;
use function realpath;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

use const DIRECTORY_SEPARATOR;

class RequireLoader implements Loader
{
    /**
     * @var string
     */
    protected $lastPath = '';

    /**
     * @var string
     */
    protected $path;

    /**
     * @param string|null $path
     */
    public function __construct($path = null)
    {
        if ($path === null) {
            $path = sys_get_temp_dir();
        }

        $this->path = realpath($path);
    }

    public function __destruct()
    {
        $files = array_diff(glob($this->path . DIRECTORY_SEPARATOR . 'Mockery_*.php') ?: [], [$this->lastPath]);

        foreach ($files as $file) {
            @unlink($file);
        }
    }

    /**
     * Load the given mock definition
     *
     * @return void
     */
    public function load(MockDefinition $definition)
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        $this->lastPath = sprintf('%s%s%s.php', $this->path, DIRECTORY_SEPARATOR, uniqid('Mockery_', false));

        file_put_contents($this->lastPath, $definition->getCode());

        if (file_exists($this->lastPath)) {
            require $this->lastPath;
        }
    }
}
