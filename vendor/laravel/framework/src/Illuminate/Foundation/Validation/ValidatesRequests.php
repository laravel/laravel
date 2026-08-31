<?php

namespace Illuminate\Foundation\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Precognition;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ValidatesRequests
{
    /**
     * Run the validation routine against the given validator.
     *
     * @param  \Illuminate\Contracts\Validation\Validator|array  $validator
     * @param  \Illuminate\Http\Request|null  $request
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateWith($validator, ?Request $request = null)
    {
        $request = $request ?: request();

        if (is_array($validator)) {
            $validator = $this->getValidationFactory()->make($request->all(), $validator);
        }

        if ($request->isPrecognitive()) {
            $validator->after(Precognition::afterValidationHook($request))
                ->setRules(
                    $request->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
                );
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, array $rules,
                             array $messages = [], array $attributes = [])
    {
        $validator = $this->getValidationFactory()->make(
            $request->all(), $rules, $messages, $attributes
        );

        if ($request->isPrecognitive()) {
            $validator->after(Precognition::afterValidationHook($request))
                ->setRules(
                    $request->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
                );
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  string  $errorBag
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateWithBag($errorBag, Request $request, array $rules,
                                    array $messages = [], array $attributes = [])
    {
        try {
            return $this->validate($request, $rules, $messages, $attributes);
        } catch (ValidationException $e) {
            $e->errorBag = $errorBag;

            throw $e;
        }
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return app(Factory::class);
    }
}
