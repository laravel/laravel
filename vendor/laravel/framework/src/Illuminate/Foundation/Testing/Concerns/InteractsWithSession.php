<?php

namespace Illuminate\Foundation\Testing\Concerns;

trait InteractsWithSession
{
    /**
     * Set the session to the given array.
     *
     * @param  array  $data
     * @return $this
     */
    public function withSession(array $data)
    {
        $this->session($data);

        return $this;
    }

    /**
     * Set the session to the given array.
     *
     * @param  array  $data
     * @return $this
     */
    public function session(array $data)
    {
        $this->startSession();

        foreach ($data as $key => $value) {
            $this->app['session']->put($key, $value);
        }

        return $this;
    }

    /**
     * Start the session for the application.
     *
     * @return $this
     */
    protected function startSession()
    {
        if (! $this->app['session']->isStarted()) {
            $this->app['session']->start();
        }

        return $this;
    }

    /**
     * Flush all of the current session data.
     *
     * @return $this
     */
    public function flushSession()
    {
        $this->startSession();

        $this->app['session']->flush();

        return $this;
    }
}
