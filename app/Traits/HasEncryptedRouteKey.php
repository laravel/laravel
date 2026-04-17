<?php

namespace App\Traits;

use App\Helpers\IdEncoder;

trait HasEncryptedRouteKey
{
    /**
     * Get the route key for the model (encrypted)
     *
     * @return string
     */
    public function getRouteKey()
    {
        return IdEncoder::encode($this->getKey());
    }

    /**
     * Retrieve the model for a bound value (decrypt the route key)
     *
     * @param mixed $value
     * @param string|null $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Try to decode the encrypted value
        $decodedId = IdEncoder::decode($value);
        
        if ($decodedId === null) {
            // Allow plain numeric IDs only when explicitly enabled.
            if (config('app.route_key_allow_plain_id', false) && is_numeric($value)) {
                $decodedId = (int) $value;
            } else {
                return null;
            }
        }

        return $this->where($field ?? $this->getRouteKeyName(), $decodedId)->first();
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param string $childType
     * @param mixed $value
     * @param string|null $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->resolveRouteBinding($value, $field);
    }
}
