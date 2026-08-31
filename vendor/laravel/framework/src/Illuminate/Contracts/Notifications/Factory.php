<?php

namespace Illuminate\Contracts\Notifications;

interface Factory
{
    /**
     * Get a channel instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null);

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification);

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function sendNow($notifiables, $notification);
}
