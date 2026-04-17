<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class IdEncoder
{
    /**
     * Encode an ID to a URL-safe encrypted string
     *
     * @param int|string $id
     * @return string
     */
    public static function encode($id): string
    {
        $encrypted = Crypt::encryptString((string) $id);
        // Make it URL-safe by replacing problematic characters
        return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
    }

    /**
     * Decode a URL-safe encrypted string back to the original ID
     *
     * @param string $encoded
     * @return int|null
     */
    public static function decode(string $encoded): ?int
    {
        try {
            // Restore base64 padding and characters
            $encrypted = base64_decode(strtr($encoded, '-_', '+/'));
            $decrypted = Crypt::decryptString($encrypted);
            return (int) $decrypted;
        } catch (DecryptException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if an encoded ID is valid
     *
     * @param string $encoded
     * @return bool
     */
    public static function isValid(string $encoded): bool
    {
        return self::decode($encoded) !== null;
    }
}
