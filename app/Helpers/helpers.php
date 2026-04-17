<?php

use App\Helpers\IdEncoder;

if (!function_exists('encode_id')) {
    /**
     * Encode an ID for use in URLs
     *
     * @param int|string $id
     * @return string
     */
    function encode_id($id): string
    {
        return IdEncoder::encode($id);
    }
}

if (!function_exists('decode_id')) {
    /**
     * Decode an encoded ID from URL
     *
     * @param string $encoded
     * @return int|null
     */
    function decode_id(string $encoded): ?int
    {
        return IdEncoder::decode($encoded);
    }
}
