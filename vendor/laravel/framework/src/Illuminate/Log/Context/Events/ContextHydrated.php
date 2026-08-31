<?php

namespace Illuminate\Log\Context\Events;

class ContextHydrated
{
    /**
     * The context instance.
     *
     * @var \Illuminate\Log\Context\Repository
     */
    public $context;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Log\Context\Repository  $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }
}
