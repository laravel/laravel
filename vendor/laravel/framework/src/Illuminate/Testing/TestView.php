<?php

namespace Illuminate\Testing;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\Constraints\SeeInOrder;
use Illuminate\View\View;
use Stringable;

class TestView implements Stringable
{
    use Macroable;

    /**
     * The original view.
     *
     * @var \Illuminate\View\View
     */
    protected $view;

    /**
     * The rendered view contents.
     *
     * @var string
     */
    protected $rendered;

    /**
     * Create a new test view instance.
     *
     * @param  \Illuminate\View\View  $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        $this->rendered = $view->render();
    }

    /**
     * Assert that the response view has a given piece of bound data.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function assertViewHas($key, $value = null)
    {
        if (is_array($key)) {
            return $this->assertViewHasAll($key);
        }

        if (is_null($value)) {
            PHPUnit::assertTrue(Arr::has($this->view->gatherData(), $key));
        } elseif ($value instanceof Closure) {
            PHPUnit::assertTrue($value(Arr::get($this->view->gatherData(), $key)));
        } elseif ($value instanceof Model) {
            PHPUnit::assertTrue($value->is(Arr::get($this->view->gatherData(), $key)));
        } elseif ($value instanceof EloquentCollection) {
            $actual = Arr::get($this->view->gatherData(), $key);

            PHPUnit::assertInstanceOf(EloquentCollection::class, $actual);
            PHPUnit::assertSameSize($value, $actual);

            $value->each(fn ($item, $index) => PHPUnit::assertTrue($actual->get($index)->is($item)));
        } else {
            PHPUnit::assertEquals($value, Arr::get($this->view->gatherData(), $key));
        }

        return $this;
    }

    /**
     * Assert that the response view has a given list of bound data.
     *
     * @param  array  $bindings
     * @return $this
     */
    public function assertViewHasAll(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_int($key)) {
                $this->assertViewHas($value);
            } else {
                $this->assertViewHas($key, $value);
            }
        }

        return $this;
    }

    /**
     * Assert that the response view is missing a piece of bound data.
     *
     * @param  string  $key
     * @return $this
     */
    public function assertViewMissing($key)
    {
        PHPUnit::assertFalse(Arr::has($this->view->gatherData(), $key));

        return $this;
    }

    /**
     * Assert that the view's rendered content is empty.
     *
     * @return $this
     */
    public function assertViewEmpty()
    {
        PHPUnit::assertEmpty($this->rendered);

        return $this;
    }

    /**
     * Assert that the given string or array of strings are contained within the view.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertSee($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        foreach ($values as $value) {
            PHPUnit::assertStringContainsString((string) $value, $this->rendered);
        }

        return $this;
    }

    /**
     * Assert that the given HTML string or array of HTML strings are contained within the view.
     *
     * @param  string|list<string>  $value
     * @return $this
     */
    public function assertSeeHtml($value)
    {
        return $this->assertSee($value, false);
    }

    /**
     * Assert that the given strings are contained in order within the view.
     *
     * @param  list<string>  $values
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeInOrder(array $values, $escape = true)
    {
        $values = $escape ? array_map(e(...), $values) : $values;

        PHPUnit::assertThat($values, new SeeInOrder($this->rendered));

        return $this;
    }

    /**
     * Assert that the given HTML strings are contained in order within the view.
     *
     * @param  list<string>  $values
     * @return $this
     */
    public function assertSeeHtmlInOrder(array $values)
    {
        return $this->assertSeeInOrder($values, false);
    }

    /**
     * Assert that the given string or array of strings are contained within the view text.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeText($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        $rendered = strip_tags($this->rendered);

        foreach ($values as $value) {
            PHPUnit::assertStringContainsString((string) $value, $rendered);
        }

        return $this;
    }

    /**
     * Assert that the given strings are contained in order within the view text.
     *
     * @param  list<string>  $values
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeTextInOrder(array $values, $escape = true)
    {
        $values = $escape ? array_map(e(...), $values) : $values;

        PHPUnit::assertThat($values, new SeeInOrder(strip_tags($this->rendered)));

        return $this;
    }

    /**
     * Assert that the given string or array of strings are not contained within the view.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertDontSee($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        foreach ($values as $value) {
            PHPUnit::assertStringNotContainsString((string) $value, $this->rendered);
        }

        return $this;
    }

    /**
     * Assert that the given HTML string or array of HTML strings are not contained within the view.
     *
     * @param  string|list<string>  $value
     * @return $this
     */
    public function assertDontSeeHtml($value)
    {
        return $this->assertDontSee($value, false);
    }

    /**
     * Assert that the given string or array of strings are not contained within the view text.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertDontSeeText($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        $rendered = strip_tags($this->rendered);

        foreach ($values as $value) {
            PHPUnit::assertStringNotContainsString((string) $value, $rendered);
        }

        return $this;
    }

    /**
     * Get the string contents of the rendered view.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->rendered;
    }
}
