<?php

namespace Faker\Extension;

/**
 * @experimental This interface is experimental and does not fall under our BC promise
 */
interface PersonExtension extends Extension
{
    public const GENDER_FEMALE = 'female';
    public const GENDER_MALE = 'male';

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @return string
     *
     * @example 'John Doe'
     */
    public function name(?string $gender = null);

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @example 'John'
     */
    public function firstName(?string $gender = null): string;

    public function firstNameMale(): string;

    public function firstNameFemale(): string;

    /**
     * @example 'Doe'
     */
    public function lastName(): string;

    /**
     * @example 'Mrs.'
     *
     * @param string|null $gender 'male', 'female' or null for any
     */
    public function title(?string $gender = null): string;

    /**
     * @example 'Mr.'
     */
    public function titleMale(): string;

    /**
     * @example 'Mrs.'
     */
    public function titleFemale(): string;
}
