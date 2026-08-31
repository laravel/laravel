<?php

namespace Illuminate\Database\Schema;

use Illuminate\Support\Fluent;

/**
 * @method ForeignKeyDefinition deferrable(bool $value = true) Set the foreign key as deferrable (PostgreSQL)
 * @method ForeignKeyDefinition initiallyImmediate(bool $value = true) Set the default time to check the constraint (PostgreSQL)
 * @method ForeignKeyDefinition lock(('none'|'shared'|'default'|'exclusive') $value) Specify the DDL lock mode for the foreign key operation (MySQL)
 * @method ForeignKeyDefinition on(string $table) Specify the referenced table
 * @method ForeignKeyDefinition onDelete(('cascade'|'restrict'|'set null'|'no action') $action) Add an ON DELETE action
 * @method ForeignKeyDefinition onUpdate(('cascade'|'restrict'|'set null'|'no action') $action) Add an ON UPDATE action
 * @method ForeignKeyDefinition references(string|string[] $columns) Specify the referenced column(s)
 */
class ForeignKeyDefinition extends Fluent
{
    /**
     * Indicate that updates should cascade.
     *
     * @return $this
     */
    public function cascadeOnUpdate()
    {
        return $this->onUpdate('cascade');
    }

    /**
     * Indicate that updates should be restricted.
     *
     * @return $this
     */
    public function restrictOnUpdate()
    {
        return $this->onUpdate('restrict');
    }

    /**
     * Indicate that updates should set the foreign key value to null.
     *
     * @return $this
     */
    public function nullOnUpdate()
    {
        return $this->onUpdate('set null');
    }

    /**
     * Indicate that updates should have "no action".
     *
     * @return $this
     */
    public function noActionOnUpdate()
    {
        return $this->onUpdate('no action');
    }

    /**
     * Indicate that deletes should cascade.
     *
     * @return $this
     */
    public function cascadeOnDelete()
    {
        return $this->onDelete('cascade');
    }

    /**
     * Indicate that deletes should be restricted.
     *
     * @return $this
     */
    public function restrictOnDelete()
    {
        return $this->onDelete('restrict');
    }

    /**
     * Indicate that deletes should set the foreign key value to null.
     *
     * @return $this
     */
    public function nullOnDelete()
    {
        return $this->onDelete('set null');
    }

    /**
     * Indicate that deletes should have "no action".
     *
     * @return $this
     */
    public function noActionOnDelete()
    {
        return $this->onDelete('no action');
    }
}
