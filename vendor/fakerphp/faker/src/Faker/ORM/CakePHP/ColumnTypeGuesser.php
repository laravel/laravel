<?php

namespace Faker\ORM\CakePHP;

class ColumnTypeGuesser
{
    protected $generator;

    public function __construct(\Faker\Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return \Closure|null
     */
    public function guessFormat($column, $table)
    {
        $generator = $this->generator;
        $schema = $table->schema();

        switch ($schema->columnType($column)) {
            case 'boolean':
                return static function () use ($generator) {
                    return $generator->boolean;
                };

            case 'integer':
                return static function () use ($generator) {
                    return $generator->numberBetween(0, 2147483647);
                };

            case 'biginteger':
                return static function () use ($generator) {
                    return $generator->numberBetween(0, PHP_INT_MAX);
                };

            case 'decimal':
            case 'float':
                return static function () use ($generator) {
                    return $generator->randomFloat();
                };

            case 'uuid':
                return static function () use ($generator) {
                    return $generator->uuid();
                };

            case 'string':
                if (method_exists($schema, 'getColumn')) {
                    $columnData = $schema->getColumn($column);
                } else {
                    $columnData = $schema->column($column);
                }
                $length = $columnData['length'];

                return static function () use ($generator, $length) {
                    return $generator->text($length);
                };

            case 'text':
                return static function () use ($generator) {
                    return $generator->text();
                };

            case 'date':
            case 'datetime':
            case 'timestamp':
            case 'time':
                return static function () use ($generator) {
                    return $generator->datetime();
                };

            case 'binary':
            default:
                return null;
        }
    }
}
