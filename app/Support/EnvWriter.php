<?php

namespace App\Support;

class EnvWriter
{
    public static function set(array $pairs): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), $envPath);
            } else {
                file_put_contents($envPath, "\n");
            }
        }
        $content = file_get_contents($envPath);
        foreach ($pairs as $key => $value) {
            $value = self::escape($value);
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= PHP_EOL."{$key}={$value}";
            }
        }
        file_put_contents($envPath, $content);
    }

    protected static function escape($value): string
    {
        if ($value === null) return '';
        $needsQuotes = str_contains($value, ' ') || str_contains($value, '#') || str_contains($value, '"');
        $escaped = str_replace(["\n", "\r"], ['', ''], $value);
        if ($needsQuotes) {
            return '"'.str_replace('"', '\\"', $escaped).'"';
        }
        return $escaped;
    }
}