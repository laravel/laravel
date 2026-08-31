<?php

namespace Illuminate\Notifications;

class Action
{
    /**
     * The action text.
     *
     * @var string
     */
    public $text;

    /**
     * The action URL.
     *
     * @var string
     */
    public $url;

    /**
     * Create a new action instance.
     *
     * @param  string  $text
     * @param  string  $url
     */
    public function __construct($text, $url)
    {
        $this->url = $url;
        $this->text = $text;
    }
}
