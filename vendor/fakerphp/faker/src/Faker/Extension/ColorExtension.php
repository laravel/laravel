<?php

namespace Faker\Extension;

/**
 * @experimental This interface is experimental and does not fall under our BC promise
 */
interface ColorExtension extends Extension
{
    /**
     * @example '#fa3cc2'
     */
    public function hexColor(): string;

    /**
     * @example '#ff0044'
     */
    public function safeHexColor(): string;

    /**
     * @example 'array(0,255,122)'
     *
     * @return int[]
     */
    public function rgbColorAsArray(): array;

    /**
     * @example '0,255,122'
     */
    public function rgbColor(): string;

    /**
     * @example 'rgb(0,255,122)'
     */
    public function rgbCssColor(): string;

    /**
     * @example 'rgba(0,255,122,0.8)'
     */
    public function rgbaCssColor(): string;

    /**
     * @example 'blue'
     */
    public function safeColorName(): string;

    /**
     * @example 'NavajoWhite'
     */
    public function colorName(): string;

    /**
     * @example '340,50,20'
     */
    public function hslColor(): string;

    /**
     * @example array(340, 50, 20)
     *
     * @return int[]
     */
    public function hslColorAsArray(): array;
}
