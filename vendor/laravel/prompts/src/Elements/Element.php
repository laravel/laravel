<?php

namespace Laravel\Prompts\Elements;

class Element
{
    public static function heading(string $text): Heading
    {
        return new Heading($text);
    }

    /**
     * @param  array<int, string>  $items
     */
    public static function bulletedList(array $items, bool $spaced = false): BulletedList
    {
        return new BulletedList($items, $spaced);
    }

    /**
     * @param  array<int, string>  $items
     */
    public static function numberedList(array $items, bool $spaced = false): NumberedList
    {
        return new NumberedList($items, $spaced);
    }

    /**
     * @param  array<string, string>  $items
     */
    public static function keyValueList(array $items): KeyValueList
    {
        return new KeyValueList($items);
    }

    public static function link(string $url, ?string $label = null, bool $underline = true): Link
    {
        return new Link($url, $label, $underline);
    }
}
