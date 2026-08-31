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

namespace League\Config\Exception;

use Nette\Schema\ValidationException as NetteException;

final class ValidationException extends InvalidConfigurationException
{
    /** @var string[] */
    private array $messages;

    public function __construct(NetteException $innerException)
    {
        parent::__construct($innerException->getMessage(), (int) $innerException->getCode(), $innerException);

        $this->messages = $innerException->getMessages();
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
