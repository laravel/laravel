<?php

namespace Illuminate\Foundation;

class Precognition
{
    /**
     * Get the "after" validation hook that can be used for precognition requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Closure
     */
    public static function afterValidationHook($request)
    {
        return function ($validator) use ($request) {
            if ($validator->messages()->isEmpty() && $request->headers->has('Precognition-Validate-Only')) {
                abort(204, headers: ['Precognition-Success' => 'true']);
            }
        };
    }
}
