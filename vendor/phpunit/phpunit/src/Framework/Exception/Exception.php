<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use function array_keys;
use function get_object_vars;
use function is_int;
use function sprintf;
use RuntimeException;
use Throwable;

/**
 * Base class for all PHPUnit Framework exceptions.
 *
 * Ensures that exceptions thrown during a test run do not leave stray
 * references behind.
 *
 * Every Exception contains a stack trace. Each stack frame contains the 'args'
 * of the called function. The function arguments can contain references to
 * instantiated objects. The references prevent the objects from being
 * destructed (until test results are eventually printed), so memory cannot be
 * freed up.
 *
 * With enabled process isolation, test results are serialized in the child
 * process and unserialized in the parent process. The stack trace of Exceptions
 * may contain objects that cannot be serialized or unserialized (e.g., PDO
 * connections). Unserializing user-space objects from the child process into
 * the parent would break the intended encapsulation of process isolation.
 *
 * @see http://fabien.potencier.org/article/9/php-serialization-stack-traces-and-exceptions
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
class Exception extends RuntimeException implements \PHPUnit\Exception
{
    /**
     * @var list<array{function: string, line?: int, file?: string, class?: class-string, type?: string, args?: list<mixed>, object?: object}>
     */
    protected array $serializableTrace;

    public function __construct(string $message = '', int|string $code = 0, ?Throwable $previous = null)
    {
        /**
         * @see https://github.com/sebastianbergmann/phpunit/issues/5965
         */
        if (!is_int($code)) {
            $message .= sprintf(
                ' (exception code: %s)',
                $code,
            );

            $code = 0;
        }

        parent::__construct($message, $code, $previous);

        $this->serializableTrace = $this->getTrace();

        foreach (array_keys($this->serializableTrace) as $key) {
            unset($this->serializableTrace[$key]['args']);
        }
    }

    public function __serialize(): array
    {
        return get_object_vars($this);
    }

    /**
     * Returns the serializable trace (without 'args').
     *
     * @return list<array{function: string, line?: int, file?: string, class?: class-string, type?: string, args?: list<mixed>, object?: object}>
     */
    public function getSerializableTrace(): array
    {
        return $this->serializableTrace;
    }
}
