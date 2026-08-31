<?php

namespace Illuminate\View\Concerns;

use InvalidArgumentException;

trait ManagesStacks
{
    /**
     * All of the finished, captured push sections.
     *
     * @var array
     */
    protected $pushes = [];

    /**
     * All of the finished, captured prepend sections.
     *
     * @var array
     */
    protected $prepends = [];

    /**
     * The stack of in-progress push sections.
     *
     * @var array
     */
    protected $pushStack = [];

    /**
     * Start injecting content into a push section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    public function startPush($section, $content = '')
    {
        if ($content === '') {
            if (ob_start()) {
                $this->pushStack[] = $section;
            }
        } else {
            $this->extendPush($section, $content);
        }
    }

    /**
     * Stop injecting content into a push section.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function stopPush()
    {
        if (empty($this->pushStack)) {
            throw new InvalidArgumentException('Cannot end a push stack without first starting one.');
        }

        return tap(array_pop($this->pushStack), function ($last) {
            $this->extendPush($last, ob_get_clean());
        });
    }

    /**
     * Append content to a given push section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendPush($section, $content)
    {
        if (! isset($this->pushes[$section])) {
            $this->pushes[$section] = [];
        }

        if (! isset($this->pushes[$section][$this->renderCount])) {
            $this->pushes[$section][$this->renderCount] = $content;
        } else {
            $this->pushes[$section][$this->renderCount] .= $content;
        }
    }

    /**
     * Start prepending content into a push section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    public function startPrepend($section, $content = '')
    {
        if ($content === '') {
            if (ob_start()) {
                $this->pushStack[] = $section;
            }
        } else {
            $this->extendPrepend($section, $content);
        }
    }

    /**
     * Stop prepending content into a push section.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function stopPrepend()
    {
        if (empty($this->pushStack)) {
            throw new InvalidArgumentException('Cannot end a prepend operation without first starting one.');
        }

        return tap(array_pop($this->pushStack), function ($last) {
            $this->extendPrepend($last, ob_get_clean());
        });
    }

    /**
     * Prepend content to a given stack.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendPrepend($section, $content)
    {
        if (! isset($this->prepends[$section])) {
            $this->prepends[$section] = [];
        }

        if (! isset($this->prepends[$section][$this->renderCount])) {
            $this->prepends[$section][$this->renderCount] = $content;
        } else {
            $this->prepends[$section][$this->renderCount] = $content.$this->prepends[$section][$this->renderCount];
        }
    }

    /**
     * Get the string contents of a push section.
     *
     * @param  string  $section
     * @param  string  $default
     * @return string
     */
    public function yieldPushContent($section, $default = '')
    {
        if ($this->isStackEmpty($section)) {
            return $default;
        }

        $output = '';

        if (isset($this->prepends[$section])) {
            $output .= implode(array_reverse($this->prepends[$section]));
        }

        if (isset($this->pushes[$section])) {
            $output .= implode($this->pushes[$section]);
        }

        return $output;
    }

    /**
     * Determine if the stack has any content in it.
     */
    public function isStackEmpty(string $section): bool
    {
        return ! isset($this->pushes[$section]) && ! isset($this->prepends[$section]);
    }

    /**
     * Flush all of the stacks.
     *
     * @return void
     */
    public function flushStacks()
    {
        $this->pushes = [];
        $this->prepends = [];
        $this->pushStack = [];
    }
}
