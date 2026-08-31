<?php

namespace Illuminate\Foundation\Testing;

trait DatabaseTransactions
{
    /**
     * Handle database transactions on the specified connections.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        $database = $this->app->make('db');

        $connections = $this->connectionsToTransact();

        $this->app->instance('db.transactions', $transactionsManager = new DatabaseTransactionsManager($connections));

        foreach ($connections as $name) {
            $connection = $database->connection($name);
            $connection->setTransactionManager($transactionsManager);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->beginTransaction();
            $connection->setEventDispatcher($dispatcher);
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $connection = $database->connection($name);
                $dispatcher = $connection->getEventDispatcher();

                $connection->unsetEventDispatcher();
                $connection->rollBack();
                $connection->setEventDispatcher($dispatcher);
                $connection->disconnect();
            }
        });
    }

    /**
     * The database connections that should have transactions.
     *
     * @return array
     */
    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact
            : [null];
    }
}
