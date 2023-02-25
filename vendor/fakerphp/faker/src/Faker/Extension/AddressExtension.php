<?php

namespace Faker\Extension;

/**
 * @experimental This interface is experimental and does not fall under our BC promise
 */
interface AddressExtension extends Extension
{
    /**
     * @example '791 Crist Parks, Sashabury, IL 86039-9874'
     */
    public function address(): string;

    /**
     * Randomly return a real city name.
     */
    public function city(): string;

    /**
     * @example 86039-9874
     */
    public function postcode(): string;

    /**
     * @example 'Crist Parks'
     */
    public function streetName(): string;

    /**
     * @example '791 Crist Parks'
     */
    public function streetAddress(): string;

    /**
     * Randomly return a building number.
     */
    public function buildingNumber(): string;
}
