<?php

namespace Illuminate\Database\Schema;

use Illuminate\Support\Fluent;

/**
 * @method $this algorithm(string $algorithm) Specify an algorithm for the index (MySQL/PostgreSQL)
 * @method $this deferrable(bool $value = true) Specify that the unique index is deferrable (PostgreSQL)
 * @method $this initiallyImmediate(bool $value = true) Specify the default time to check the unique index constraint (PostgreSQL)
 * @method $this language(string $language) Specify a language for the full text index (PostgreSQL)
 * @method $this lock(('none'|'shared'|'default'|'exclusive') $value) Specify the DDL lock mode for the index operation (MySQL)
 * @method $this nullsNotDistinct(bool $value = true) Specify that the null values should not be treated as distinct (PostgreSQL)
 * @method $this online(bool $value = true) Specify that index creation should not lock the table (PostgreSQL/SqlServer)
 */
class IndexDefinition extends Fluent
{
    //
}
