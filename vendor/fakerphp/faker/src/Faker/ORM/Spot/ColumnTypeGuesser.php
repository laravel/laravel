<?php

namespace Faker\ORM\Spot;

use Faker\Generator;

class ColumnTypeGuesser
{
    protected $generator;

    /**
     * ColumnTypeGuesser constructor.
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return \Closure|null
     */
    public function guessFormat(array $field)
    {
        $generator = $this->generator;
        $type = $field['type'];

        switch ($type) {
            case 'boolean':
                return static function () use ($generator) {
                    return $generator->boolean;
                };

            case 'decimal':
                $size = $field['precision'] ?? 2;

                return static function () use ($generator, $size) {
                    return $generator->randomNumber($size + 2) / 100;
                };

            case 'smallint':
                return static function () use ($generator) {
                    return $generator->numberBetween(0, 65535);
                };

            case 'integer':
                return static function () use ($generator) {
                    return $generator->numberBetween(0, 2147483647);
                };

            case 'bigint':
                return static function () use ($generator) {
                    return $generator->numberBetween(0, PHP_INT_MAX);
                };

            case 'float':
                return static function () use ($generator) {
                    return $generator->randomFloat(null, 0, 4294967295);
                };

            case 'string':
                $size = $field['length'] ?? 255;

                return static function () use ($generator, $size) {
                    return $generator->text($size);
                };

            case 'text':
                return static function () use ($generator) {
                    return $generator->text;
                };

            case 'datetime':
            case 'date':
            case 'time':
                return static function () use ($generator) {
                    return $generator->datetime;
                };

            default:
                // no smart way to guess what the user expects here
                return null;
        }
    }
}
