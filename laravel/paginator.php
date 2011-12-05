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
	public $page;

	/**
	 * The last page available for the result set.
	 *
	 * @var int
	 */
	public $last;

	/**
	 * The total number of results.
	 *
	 * @var int
	 */
	public $total;

	/**
	 * The number of items per page.
	 *
	 * @var int
	 */
	public $per_page;

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
	 * The language that should be used when creating the pagination links.
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * The "dots" element used in the pagination slider.
	 *
	 * @var string
	 */
	protected $dots = '<span class="dots">...</span>';

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

		$last = ceil($total / $per_page);

		return new static($results, $page, $total, $per_page, $last);
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

		return (static::valid($page)) ? $page : 1;
	}

	/**
	 * Determine if a given page number is a valid page.
	 *
	 * A valid page must be greater than or equal to one and a valid integer.
	 *
	 * @param  int   $page
	 * @return bool
	 */
	protected static function valid($page)
	{
		return $page >= 1 and filter_var($page, FILTER_VALIDATE_INT) !== false;
	}

	/**
	 * Create the HTML pagination links.
	 *
	 * Typically, an intelligent, "sliding" window of links will be rendered based
	 * on the total number of pages, the current page, and the number of adjacent
	 * pages that should rendered. This creates a beautiful paginator similar to
	 * that of Google's.
	 *
	 * Example: 1 2 ... 23 24 25 [26] 27 28 29 ... 51 52
	 *
	 * If you wish to render only certain elements of the pagination control,
	 * explore some of the other public methods available on the instance.
	 *
	 * <code>
	 *		// Render the pagination links
	 *		echo $paginator->links();
	 *
	 *		// Render the pagination links using a given window size
	 *		echo $paginator->links(5);
	 * </code>
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	public function links($adjacent = 3)
	{
		if ($this->last <= 1) return '';

		// The hard-coded seven is to account for all of the constant elements in a
		// sliding range, such as the current page, the two ellipses, and the two
		// beginning and ending pages.
		//
		// If there are not enough pages to make the creation of a slider possible
		// based on the adjacent pages, we will simply display all of the pages.
		// Otherwise, we will create a "truncating" slider which displays a nice
		// window of pages based on the current page.
		if ($this->last < 7 + ($adjacent * 2))
		{
			$links = $this->range(1, $this->last);
		}
		else
		{
			$links = $this->slider($adjacent);
		}

		$content = $this->previous().' '.$links.' '.$this->next();

		return '<div class="pagination">'.$content.'</div>';
	}

	/**
	 * Build sliding list of HTML numeric page links.
	 *
	 * This method is very similar to the "links" method, only it does not
	 * render the "first" and "last" pagination links, but only the pages.
	 *
	 * <code>
	 *		// Render the pagination slider
	 *		echo $paginator->slider();
	 *
	 *		// Render the pagination slider using a given window size
	 *		echo $paginator->slider(5);
	 * </code>
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	public function slider($adjacent = 3)
	{
		$window = $adjacent * 2;

		// If the current page is so close to the beginning that we do not have
		// room to create a full sliding window, we will only show the first
		// several pages, followed by the ending section of the slider.
		//
		// Likewise, if the page is very close to the end, we will create the
		// beginning of the slider, but just show the last several pages at
		// the end of the slider.
		//
		// Example: 1 [2] 3 4 5 6 ... 23 24
		if ($this->page <= $window)
		{
			return $this->range(1, $window + 2).' '.$this->ending();
		}
		// Example: 1 2 ... 32 33 34 35 [36] 37
		elseif ($this->page >= $this->last - $window)
		{
			return $this->beginning().' '.$this->range($this->last - $window - 2, $this->last);
		}

		// Example: 1 2 ... 23 24 25 [26] 27 28 29 ... 51 52
		$content = $this->range($this->page - $adjacent, $this->page + $adjacent);

		return $this->beginning().' '.$content.' '.$this->ending();
	}

	/**
	 * Generate the "previous" HTML link.
	 *
	 * <code>
	 *		// Create the "previous" pagination element
	 *		echo $paginator->previous();
	 *
	 *		// Create the "previous" pagination element with custom text
	 *		echo $paginator->previous('Go Back');
	 * </code>
	 *
	 * @return string
	 */
	public function previous($text = null)
	{
		$disabled = function($page) { return $page <= 1; };

		return $this->element(__FUNCTION__, $this->page - 1, $text, $disabled);
	}

	/**
	 * Generate the "next" HTML link.
	 *
	 * <code>
	 *		// Create the "next" pagination element
	 *		echo $paginator->next();
	 *
	 *		// Create the "next" pagination element with custom text
	 *		echo $paginator->next('Skip Forwards');
	 * </code>
	 *
	 * @return string
	 */
	public function next($text = null)
	{
		$disabled = function($page, $last) { return $page >= $last; };

		return $this->element(__FUNCTION__, $this->page + 1, $text, $disabled);
	}

	/**
	 * Create a chronological pagination element, such as a "previous" or "next" link.
	 *
	 * @param  string   $element
	 * @param  int      $page
	 * @param  string   $text
	 * @param  Closure  $disabled
	 * @return string
	 */
	protected function element($element, $page, $text, $disabled)
	{
		$class = "{$element}_page";

		if (is_null($text)) $text = Lang::line("pagination.{$element}")->get($this->language);

		// Each consumer of this method provides a "disabled" Closure which can
		// be used to determine if the element should be a span element or an
		// actual link. For example, if the current page is the first page,
		// the "first" element should be a span instead of a link.
		if ($disabled($this->page, $this->last))
		{
			return HTML::span($text, array('class' => "{$class} disabled"));
		}
		else
		{
			return $this->link($page, $text, $class);
		}
	}

	/**
	 * Build the first two page links for a sliding page range.
	 *
	 * @return string
	 */
	protected function beginning()
	{
		return $this->range(1, 2).' '.$this->dots;
	}

	/**
	 * Build the last two page links for a sliding page range.
	 *
	 * @return string
	 */
	protected function ending()
	{
		return $this->dots.' '.$this->range($this->last - 1, $this->last);
	}

	/**
	 * Build a range of numeric pagination links.
	 *
	 * For the current page, an HTML span element will be generated instead of a link.
	 *
	 * @param  int     $start
	 * @param  int     $end
	 * @return string
	 */
	protected function range($start, $end)
	{
		$pages = array();

		// To generate the range of page links, we will iterate through each page
		// and, if the current page matches the page, we will generate a span,
		// otherwise we will generate a link for the page. The span elements
		// will be assigned the "current" CSS class for convenient styling.
		for ($page = $start; $page <= $end; $page++)
		{
			if ($this->page == $page)
			{
				$pages[] = HTML::span($page, array('class' => 'current'));
			}
			else
			{
				$pages[] = $this->link($page, $page, null);
			}
		}

		return implode(' ', $pages);
	}

	/**
	 * Create a HTML page link.
	 *
	 * @param  int     $page
	 * @param  string  $text
	 * @param  string  $attributes
	 * @return string
	 */
	protected function link($page, $text, $class)
	{
		$query = '?page='.$page.$this->appendage($this->appends);

		return HTML::link(URI::current().$query, $text, compact('class'), Request::secure());
	}

	/**
	 * Create the "appendage" that should be attached to every pagination link.
	 *
	 * The developer may assign an array of values that will be converted to a
	 * query string and attached to every pagination link. This allows simple
	 * implementation of sorting or other things the developer may need.
	 *
	 * @param  array   $appends
	 * @return string
	 */
	protected function appendage($appends)
	{
		if ( ! is_null($this->appendage))
		{
			return $this->appendage;
		}

		return $this->appendage = (count($appends) > 0) ? '&'.http_build_query($appends) : '';
	}

	/**
	 * Set the items that should be appended to the link query strings.
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
	 * Set the language that should be used when creating the pagination links.
	 *
	 * @param  string     $language
	 * @return Paginator
	 */
	public function speaks($language)
	{
		$this->language = $language;
		return $this;
	}

}