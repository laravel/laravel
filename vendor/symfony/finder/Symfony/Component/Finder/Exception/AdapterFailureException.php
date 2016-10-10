<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Exception;

use Symfony\Component\Finder\Adapter\AdapterInterface;

/**
 * Base exception for all adapter failures.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class AdapterFailureException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var \Symfony\Component\Finder\Adapter\AdapterInterface
     */
    private $adapter;

    /**
     * @param AdapterInterface $adapter
     * @param string|null      $message
     * @param \Exception|null  $previous
     */
    public function __construct(AdapterInterface $adapter, $message = null, \Exception $previous = null)
    {
        $this->adapter = $adapter;
        parent::__construct($message ?: 'Search failed with "'.$adapter->getName().'" adapter.', $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
