<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Inspector;

use Whoops\Exception\Inspector;

class InspectorFactory implements InspectorFactoryInterface
{
    /**
     * @param \Throwable $exception
     * @return InspectorInterface
     */
    public function create($exception)
    {
        return new Inspector($exception, $this);
    }
}
