<?php namespace Laravel;

class Paginator {

	/**
	 * The results for the current page.
	 *
	 * @var array
	 */
	public $results;

	/**
	 * The current page.
	 *
	 * @var int
	 */
	protected $page;

	/**
	 * The last page available for the result set.
	 *
	 * @var int
	 */
	protected $last;

	/**
	 * The total number of results.
	 *
	 * @var int
	 */
	protected $total;

	/**
	 * The number of items per page.
	 *
	 * @var int
	 */
	protected $per_page;

	/**
	 * The values that should be appended to the end of the link query strings.
	 *
	 * @var array
	 */
	protected $appends;

	/**
	 * The compiled appendage that will be appended to the links.
	 *
	 * This consists of a sprintf format  with a page place-holder and query string.
	 *
	 * @var string
	 */
	protected $appendage;

	/**
	 * The pagination elements that will be generated.
	 *
	 * @var array
	 */
	protected $elements = array('first', 'previous', 'status', 'next', 'last');

	/**
	 * Create a new Paginator instance.
	 *
	 * @param  array  $results
	 * @param  int    $last
	 * @param  int    $page
	 * @param  int    $total
	 * @param  int    $per_page
	 * @return void
	 */
	protected function __construct($results, $page, $total, $per_page, $last)
	{
		$this->page = $page;
		$this->last = $last;
		$this->total = $total;
		$this->results = $results;
		$this->per_page = $per_page;
	}

	/**
	 * Create a new Paginator instance.
	 *
	 * @param  array      $results
	 * @param  int        $total
	 * @param  int        $per_page
	 * @return Paginator
	 */
	public static function make($results, $total, $per_page)
	{
		$page = static::page($total, $per_page);

		$last_page = ceil($total / $per_page);

		return new static($results, $page, $total, $per_page, $last_page);
	}

	/**
	 * Get the current page from the request query string.
	 *
	 * @param  int  $total
	 * @param  int  $per_page
	 * @return int
	 */
	public static function page($total, $per_page)
	{
		$page = Input::get('page', 1);

		// The page will be validated and adjusted if it is less than one or greater
		// than the last page. For example, if the current page is not an integer or
		// less than one, one will be returned. If the current page is greater than
		// the last page, the last page will be returned.
		if (is_numeric($page) and $page > $last = ceil($total / $per_page))
		{
			return ($last > 0) ? $last : 1;
		}

		return ($page < 1 or filter_var($page, FILTER_VALIDATE_INT) === false) ? 1 : $page;
	}

	/**
	 * Create the HTML pagination links.
	 *
	 * @return string
	 */
	public function links()
	{
		if ($this->last <= 1) return '';

		// Each pagination element is created by an element method. This allows
		// us to keep this class clean and simple, because pagination code can
		// become a mess. We would rather keep it simple and beautiful.
		foreach ($this->elements as $element)
		{
			$elements[] = $this->$element(Lang::line("pagination.{$element}")->get());
		}

		return '<div class="pagination">'.implode(' ', $elements).'</div>'.PHP_EOL;
	}

	/**
	 * Get the "status" pagination element.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function status($text)
	{
		return str_replace(array(':current', ':last'), array($this->page, $this->last), $text);
	}

	/**
	 * Create the "first" pagination element.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function first($text)
	{
		return $this->backwards(__FUNCTION__, $text, 1);
	}

	/**
	 * Create the "previous" pagination element.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function previous($text)
	{
		return $this->backwards(__FUNCTION__, $text, $this->page - 1);
	}

	/**
	 * Create the "next" pagination element.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function next($text)
	{
		return $this->forwards(__FUNCTION__, $text, $this->page + 1);
	}

	/**
	 * Create the "last" pagination element.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function last($text)
	{
		return $this->forwards(__FUNCTION__, $text, $this->last);
	}

	/**
	 * Create a "backwards" paginatino element.
	 *
	 * This function handles the creation of the first and previous elements.
	 *
	 * @param  string  $element
	 * @param  string  $text
	 * @param  int     $last
	 * @return string
	 */
	protected function backwards($element, $text, $last)
	{
		$disabler = function($page) { return $page <= 1; };

		return $this->element($element, $text, $last, $disabler);
	}

	/**
	 * Create a "forwards" paginatino element.
	 *
	 * This function handles the creation of the next and last elements.
	 *
	 * @param  string  $element
	 * @param  string  $text
	 * @param  int     $last
	 * @return string
	 */
	protected function forwards($element, $text, $last)
	{
		$disabler = function($page, $last) { return $page >= $last; };

		return $this->element($element, $text, $last, $disabler);
	}

	/**
	 * Create a chronological pagination element.
	 *
	 * @param  string   $element
	 * @param  string   $text
	 * @param  int      $page
	 * @param  Closure  $disabler
	 * @return string
	 */
	protected function element($element, $text, $page, $disabler)
	{
		$class = "{$element}_page";

		if ($disabler($this->page, $this->last))
		{
			return HTML::span($text, array('class' => "disabled {$class}"));
		}
		else
		{
			// We will assume the page links should use HTTPS if the current request
			// is also using HTTPS. Since pagination links automatically point to
			// the current URI, this makes pretty good sense.
			list($uri, $secure) = array(Request::uri(), Request::secure());

			$appendage = $this->appendage($element, $page);

			return HTML::link($uri.$appendage, $text, array('class' => $class), $secure);
		}
	}

	/**
	 * Create the pagination link "appendage" for an element.
	 *
	 * @param  string  $element
	 * @param  int     $page
	 * @return string
	 */
	protected function appendage($element, $page)
	{
		if (is_null($this->appendage))
		{
			$this->appendage = '?page=%s'.http_build_query((array) $this->appends);
		}

		return sprintf($this->appendage, $page);
	}

	/**
	 * Set the items that should be appended to the link query strings.
	 *
	 * This provides a convenient method of maintaining sort or passing other information
	 * to the route handling pagination.
	 *
	 * @param  array      $values
	 * @return Paginator
	 */
	public function appends($values)
	{
		$this->appends = $values;
		return $this;
	}

	/**
	 * Set the elements that should be included when creating the pagination links.
	 *
	 * The available elements are "first", "previous", "status", "next", and "last".
	 *
	 * @param  array   $elements
	 * @return string
	 */
	public function elements($elements)
	{
		$this->elements = $elements;
		return $this;
	}

}