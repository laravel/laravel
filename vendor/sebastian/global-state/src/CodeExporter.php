<?php declare(strict_types=1);
/*
 * This file is part of sebastian/global-state.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\GlobalState;

use const PHP_EOL;
use function is_array;
use function is_scalar;
use function serialize;
use function sprintf;
use function var_export;

final class CodeExporter
{
    public function constants(Snapshot $snapshot): string
    {
        $result = '';

        foreach ($snapshot->constants() as $name => $value) {
            $result .= sprintf(
                'if (!defined(\'%s\')) define(\'%s\', %s);' . "\n",
                $name,
                $name,
                $this->exportVariable($value),
            );
        }

        return $result;
    }

    public function globalVariables(Snapshot $snapshot): string
    {
        $result = <<<'EOT'
call_user_func(
    function ()
    {
        foreach (array_keys($GLOBALS) as $key) {
            unset($GLOBALS[$key]);
        }
    }
);


EOT;

        foreach ($snapshot->globalVariables() as $name => $value) {
            $result .= sprintf(
                '$GLOBALS[%s] = %s;' . PHP_EOL,
                $this->exportVariable($name),
                $this->exportVariable($value),
            );
        }

        return $result;
    }

    public function iniSettings(Snapshot $snapshot): string
    {
        $result = '';

        foreach ($snapshot->iniSettings() as $key => $value) {
            $result .= sprintf(
                '@ini_set(%s, %s);' . "\n",
                $this->exportVariable($key),
                $this->exportVariable($value),
            );
        }

        return $result;
    }

    private function exportVariable(mixed $variable): string
    {
        if (is_scalar($variable) || null === $variable ||
            (is_array($variable) && $this->arrayOnlyContainsScalars($variable))) {
            return var_export($variable, true);
        }

        return 'unserialize(' . var_export(serialize($variable), true) . ')';
    }

    /**
     * @param array<mixed> $array
     */
    private function arrayOnlyContainsScalars(array $array): bool
    {
        $result = true;

        foreach ($array as $element) {
            if (is_array($element)) {
                $result = $this->arrayOnlyContainsScalars($element);
            } elseif (!is_scalar($element) && null !== $element) {
                $result = false;
            }

            if ($result === false) {
                break;
            }
        }

        return $result;
    }
}
