<?php

namespace Illuminate\Database;

class DatabaseTransactionRecord
{
    /**
     * The name of the database connection.
     *
     * @var string
     */
    public $connection;

    /**
     * The transaction level.
     *
     * @var int
     */
    public $level;

    /**
     * The parent instance of this transaction.
     *
     * @var \Illuminate\Database\DatabaseTransactionRecord
     */
    public $parent;

    /**
     * The callbacks that should be executed after committing.
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * The callbacks that should be executed after rollback.
     *
     * @var array
     */
    protected $callbacksForRollback = [];

    /**
     * Create a new database transaction record instance.
     *
     * @param  string  $connection
     * @param  int  $level
     * @param  \Illuminate\Database\DatabaseTransactionRecord|null  $parent
     */
    public function __construct($connection, $level, ?DatabaseTransactionRecord $parent = null)
    {
        $this->connection = $connection;
        $this->level = $level;
        $this->parent = $parent;
    }

    /**
     * Register a callback to be executed after committing.
     *
     * @param  callable  $callback
     * @return void
     */
    public function addCallback($callback)
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Register a callback to be executed after rollback.
     *
     * @param  callable  $callback
     * @return void
     */
    public function addCallbackForRollback($callback)
    {
        $this->callbacksForRollback[] = $callback;
    }

    /**
     * Execute all of the callbacks.
     *
     * @return void
     */
    public function executeCallbacks()
    {
        foreach ($this->callbacks as $callback) {
            $callback();
        }
    }

    /**
     * Execute all of the callbacks for rollback.
     *
     * @return void
     */
    public function executeCallbacksForRollback()
    {
        foreach ($this->callbacksForRollback as $callback) {
            $callback();
        }
    }

    /**
     * Get all of the callbacks.
     *
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * Get all of the callbacks for rollback.
     *
     * @return array
     */
    public function getCallbacksForRollback()
    {
        return $this->callbacksForRollback;
    }
}
