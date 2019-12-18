<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPhone implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     * @throws \Exception
     */
    public function passes($attribute, $value)
    {
        $key = config('numverify.api_key');
        $code = config('numverify.country_code');

        // Initialize CURL:
        $ch = curl_init(
            'http://apilayer.net/api/validate'.
            '?access_key='.$key.
            '&number='.$value.
            '&country_code='.$code);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $result = json_decode($json, true);

        if (isset($result['valid'])) {
            return boolval($result['valid']);
        } elseif (isset($result['error'])) {
            throw new \Exception($result['error']['info']);
        } else {
            throw new \Exception('Unknown error during phone validation');
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.valid_phone');
    }
}
