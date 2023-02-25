<?php

namespace Faker\Extension;

/**
 * @experimental This interface is experimental and does not fall under our BC promise
 */
interface BloodExtension extends Extension
{
    /**
     * Get an actual blood type
     *
     * @example 'AB'
     */
    public function bloodType(): string;

    /**
     * Get a random resis value
     *
     * @example '+'
     */
    public function bloodRh(): string;

    /**
     * Get a full blood group
     *
     * @example 'AB+'
     */
    public function bloodGroup(): string;
}
