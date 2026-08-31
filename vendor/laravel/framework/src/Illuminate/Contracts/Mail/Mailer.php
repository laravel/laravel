<?php

namespace Illuminate\Contracts\Mail;

interface Mailer
{
    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function to($users);

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function bcc($users);

    /**
     * Send a new message with only a raw text part.
     *
     * @param  string  $text
     * @param  mixed  $callback
     * @return \Illuminate\Mail\SentMessage|null
     */
    public function raw($text, $callback);

    /**
     * Send a new message using a view.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable|string|array  $view
     * @param  array  $data
     * @param  \Closure|string|null  $callback
     * @return \Illuminate\Mail\SentMessage|null
     */
    public function send($view, array $data = [], $callback = null);

    /**
     * Send a new message synchronously using a view.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable|string|array  $mailable
     * @param  array  $data
     * @param  \Closure|string|null  $callback
     * @return \Illuminate\Mail\SentMessage|null
     */
    public function sendNow($mailable, array $data = [], $callback = null);
}
