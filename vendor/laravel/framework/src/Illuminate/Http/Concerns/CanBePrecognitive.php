<?php

namespace Illuminate\Http\Concerns;

use Illuminate\Support\Collection;

trait CanBePrecognitive
{
    /**
     * Filter the given array of rules into an array of rules that are included in precognitive headers.
     *
     * @param  array  $rules
     * @return array
     */
    public function filterPrecognitiveRules($rules)
    {
        if (! $this->headers->has('Precognition-Validate-Only')) {
            return $rules;
        }

        $validateOnly = explode(',', $this->header('Precognition-Validate-Only'));

        return (new Collection($rules))
            ->filter(fn ($rule, $attribute) => $this->shouldValidatePrecognitiveAttribute($attribute, $validateOnly))
            ->all();
    }

    /**
     * Determine if the given attribute should be validated.
     *
     * @param  string  $attribute
     * @param  array  $validateOnly
     * @return bool
     */
    protected function shouldValidatePrecognitiveAttribute($attribute, $validateOnly)
    {
        foreach ($validateOnly as $pattern) {
            $regex = '/^'.str_replace('\*', '[^.]+', preg_quote($pattern, '/')).'$/';

            if (preg_match($regex, $attribute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the request is attempting to be precognitive.
     *
     * @return bool
     */
    public function isAttemptingPrecognition()
    {
        return $this->header('Precognition') === 'true';
    }

    /**
     * Determine if the request is precognitive.
     *
     * @return bool
     */
    public function isPrecognitive()
    {
        return $this->attributes->get('precognitive', false);
    }
}
