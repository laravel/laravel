<?php

namespace Illuminate\Foundation\Http\Middleware\Concerns;

trait ExcludesPaths
{
    /**
     * Determine if the request has a URI that should be excluded.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->getExcludedPaths() as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the URIs that should be excluded.
     *
     * @return array
     */
    public function getExcludedPaths()
    {
        return $this->except ?? [];
    }
}
