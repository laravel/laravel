<?php

namespace App\Helpers;

use Hashids\Hashids;

class Common
{
    static public function getHashids() {
        return new Hashids(
            env('HASHIDS_SECRET'),
            env('HASHIDS_LENGTH'),
            env('HASHIDS_CHARS')
        );
    }

    static public function encodeIds($id)
    {
        return self::getHashids()->encode($id);
    }

    static public function decodeIds($encoded)
    {
        $decoded = self::getHashids()->decode($encoded);
        if (empty($decoded)) {
            return false;
        }
        if (count($decoded) === 1) {
            return $decoded[0];
        }
        return $decoded;
    }
}
