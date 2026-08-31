<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon;

use JsonSerializable;

class Language implements JsonSerializable
{
    protected static ?array $languagesNames = null;

    protected static ?array $regionsNames = null;

    protected string $id;

    protected string $code;

    protected ?string $variant = null;

    protected ?string $region = null;

    protected ?array $names = null;

    protected ?string $isoName = null;

    protected ?string $nativeName = null;

    public function __construct(string $id)
    {
        $this->id = str_replace('-', '_', $id);
        $parts = explode('_', $this->id);
        $this->code = $parts[0];

        if (isset($parts[1])) {
            if (!preg_match('/^[A-Z]+$/', $parts[1])) {
                $this->variant = $parts[1];
                $parts[1] = $parts[2] ?? null;
            }
            if ($parts[1]) {
                $this->region = $parts[1];
            }
        }
    }

    /**
     * Get the list of the known languages.
     *
     * @return array
     */
    public static function all(): array
    {
        static::$languagesNames ??= require __DIR__.'/List/languages.php';

        return static::$languagesNames;
    }

    /**
     * Get the list of the known regions.
     *
     * ⚠ ISO 3166-2 short name provided with no warranty, should not
     * be used for any purpose to show official state names.
     */
    public static function regions(): array
    {
        static::$regionsNames ??= require __DIR__.'/List/regions.php';

        return static::$regionsNames;
    }

    /**
     * Get both isoName and nativeName as an array.
     */
    public function getNames(): array
    {
        $this->names ??= static::all()[$this->code] ?? [
            'isoName' => $this->code,
            'nativeName' => $this->code,
        ];

        return $this->names;
    }

    /**
     * Returns the original locale ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the code of the locale "en"/"fr".
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Returns the variant code such as cyrl/latn.
     */
    public function getVariant(): ?string
    {
        return $this->variant;
    }

    /**
     * Returns the variant such as Cyrillic/Latin.
     */
    public function getVariantName(): ?string
    {
        if ($this->variant === 'Latn') {
            return 'Latin';
        }

        if ($this->variant === 'Cyrl') {
            return 'Cyrillic';
        }

        return $this->variant;
    }

    /**
     * Returns the region part of the locale.
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * Returns the region name for the current language.
     *
     * ⚠ ISO 3166-2 short name provided with no warranty, should not
     * be used for any purpose to show official state names.
     */
    public function getRegionName(): ?string
    {
        return $this->region ? (static::regions()[$this->region] ?? $this->region) : null;
    }

    /**
     * Returns the long ISO language name.
     */
    public function getFullIsoName(): string
    {
        $this->isoName ??= $this->getNames()['isoName'];

        return $this->isoName;
    }

    /**
     * Set the ISO language name.
     */
    public function setIsoName(string $isoName): static
    {
        $this->isoName = $isoName;

        return $this;
    }

    /**
     * Return the full name of the language in this language.
     */
    public function getFullNativeName(): string
    {
        $this->nativeName ??= $this->getNames()['nativeName'];

        return $this->nativeName;
    }

    /**
     * Set the name of the language in this language.
     */
    public function setNativeName(string $nativeName): static
    {
        $this->nativeName = $nativeName;

        return $this;
    }

    /**
     * Returns the short ISO language name.
     */
    public function getIsoName(): string
    {
        $name = $this->getFullIsoName();

        return trim(strstr($name, ',', true) ?: $name);
    }

    /**
     * Get the short name of the language in this language.
     */
    public function getNativeName(): string
    {
        $name = $this->getFullNativeName();

        return trim(strstr($name, ',', true) ?: $name);
    }

    /**
     * Get a string with short ISO name, region in parentheses if applicable, variant in parentheses if applicable.
     */
    public function getIsoDescription(): string
    {
        $region = $this->getRegionName();
        $variant = $this->getVariantName();

        return $this->getIsoName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
    }

    /**
     * Get a string with short native name, region in parentheses if applicable, variant in parentheses if applicable.
     */
    public function getNativeDescription(): string
    {
        $region = $this->getRegionName();
        $variant = $this->getVariantName();

        return $this->getNativeName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
    }

    /**
     * Get a string with long ISO name, region in parentheses if applicable, variant in parentheses if applicable.
     */
    public function getFullIsoDescription(): string
    {
        $region = $this->getRegionName();
        $variant = $this->getVariantName();

        return $this->getFullIsoName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
    }

    /**
     * Get a string with long native name, region in parentheses if applicable, variant in parentheses if applicable.
     */
    public function getFullNativeDescription(): string
    {
        $region = $this->getRegionName();
        $variant = $this->getVariantName();

        return $this->getFullNativeName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
    }

    /**
     * Returns the original locale ID.
     */
    public function __toString(): string
    {
        return $this->getId();
    }

    /**
     * Get a string with short ISO name, region in parentheses if applicable, variant in parentheses if applicable.
     */
    public function jsonSerialize(): string
    {
        return $this->getIsoDescription();
    }
}
