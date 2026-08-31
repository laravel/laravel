<?php

declare(strict_types=1);

namespace League\Flysystem;

use function array_diff_key;
use function array_flip;
use function array_merge;

class Config
{
    public const OPTION_COPY_IDENTICAL_PATH = 'copy_destination_same_as_source';
    public const OPTION_MOVE_IDENTICAL_PATH = 'move_destination_same_as_source';
    public const OPTION_VISIBILITY = 'visibility';
    public const OPTION_DIRECTORY_VISIBILITY = 'directory_visibility';
    public const OPTION_RETAIN_VISIBILITY = 'retain_visibility';

    public function __construct(private array $options = [])
    {
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $property, $default = null)
    {
        return $this->options[$property] ?? $default;
    }

    public function extend(array $options): Config
    {
        return new Config(array_merge($this->options, $options));
    }

    public function withDefaults(array $defaults): Config
    {
        return new Config($this->options + $defaults);
    }

    public function toArray(): array
    {
        return $this->options;
    }

    public function withSetting(string $property, mixed $setting): Config
    {
        return $this->extend([$property => $setting]);
    }

    public function withoutSettings(string ...$settings): Config
    {
        return new Config(array_diff_key($this->options, array_flip($settings)));
    }
}
