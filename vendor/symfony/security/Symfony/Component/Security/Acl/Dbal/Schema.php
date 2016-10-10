<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Dbal;

use Doctrine\DBAL\Schema\Schema as BaseSchema;
use Doctrine\DBAL\Connection;

/**
 * The schema used for the ACL system.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Schema extends BaseSchema
{
    protected $options;

    /**
     * Constructor
     *
     * @param array      $options    the names for tables
     * @param Connection $connection
     */
    public function __construct(array $options, Connection $connection = null)
    {
        $schemaConfig = null === $connection ? null : $connection->getSchemaManager()->createSchemaConfig();

        parent::__construct(array(), array(), $schemaConfig);

        $this->options = $options;

        $this->addClassTable();
        $this->addSecurityIdentitiesTable();
        $this->addObjectIdentitiesTable();
        $this->addObjectIdentityAncestorsTable();
        $this->addEntryTable();
    }

    /**
     * Merges ACL schema with the given schema.
     *
     * @param BaseSchema $schema
     */
    public function addToSchema(BaseSchema $schema)
    {
        foreach ($this->getTables() as $table) {
            $schema->_addTable($table);
        }

        foreach ($this->getSequences() as $sequence) {
            $schema->_addSequence($sequence);
        }
    }

    /**
     * Adds the class table to the schema
     */
    protected function addClassTable()
    {
        $table = $this->createTable($this->options['class_table_name']);
        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => 'auto'));
        $table->addColumn('class_type', 'string', array('length' => 200));
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('class_type'));
    }

    /**
     * Adds the entry table to the schema
     */
    protected function addEntryTable()
    {
        $table = $this->createTable($this->options['entry_table_name']);

        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => 'auto'));
        $table->addColumn('class_id', 'integer', array('unsigned' => true));
        $table->addColumn('object_identity_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $table->addColumn('field_name', 'string', array('length' => 50, 'notnull' => false));
        $table->addColumn('ace_order', 'smallint', array('unsigned' => true));
        $table->addColumn('security_identity_id', 'integer', array('unsigned' => true));
        $table->addColumn('mask', 'integer');
        $table->addColumn('granting', 'boolean');
        $table->addColumn('granting_strategy', 'string', array('length' => 30));
        $table->addColumn('audit_success', 'boolean');
        $table->addColumn('audit_failure', 'boolean');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('class_id', 'object_identity_id', 'field_name', 'ace_order'));
        $table->addIndex(array('class_id', 'object_identity_id', 'security_identity_id'));

        $table->addForeignKeyConstraint($this->getTable($this->options['class_table_name']), array('class_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
        $table->addForeignKeyConstraint($this->getTable($this->options['oid_table_name']), array('object_identity_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
        $table->addForeignKeyConstraint($this->getTable($this->options['sid_table_name']), array('security_identity_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
    }

    /**
     * Adds the object identity table to the schema
     */
    protected function addObjectIdentitiesTable()
    {
        $table = $this->createTable($this->options['oid_table_name']);

        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => 'auto'));
        $table->addColumn('class_id', 'integer', array('unsigned' => true));
        $table->addColumn('object_identifier', 'string', array('length' => 100));
        $table->addColumn('parent_object_identity_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $table->addColumn('entries_inheriting', 'boolean');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('object_identifier', 'class_id'));
        $table->addIndex(array('parent_object_identity_id'));

        $table->addForeignKeyConstraint($table, array('parent_object_identity_id'), array('id'));
    }

    /**
     * Adds the object identity relation table to the schema
     */
    protected function addObjectIdentityAncestorsTable()
    {
        $table = $this->createTable($this->options['oid_ancestors_table_name']);

        $table->addColumn('object_identity_id', 'integer', array('unsigned' => true));
        $table->addColumn('ancestor_id', 'integer', array('unsigned' => true));

        $table->setPrimaryKey(array('object_identity_id', 'ancestor_id'));

        $oidTable = $this->getTable($this->options['oid_table_name']);
        $table->addForeignKeyConstraint($oidTable, array('object_identity_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
        $table->addForeignKeyConstraint($oidTable, array('ancestor_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
    }

    /**
     * Adds the security identity table to the schema
     */
    protected function addSecurityIdentitiesTable()
    {
        $table = $this->createTable($this->options['sid_table_name']);

        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => 'auto'));
        $table->addColumn('identifier', 'string', array('length' => 200));
        $table->addColumn('username', 'boolean');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('identifier', 'username'));
    }
}
