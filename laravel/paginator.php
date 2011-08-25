<?php namespace Laravel;

class Paginator {

	/**
	 * The results for the current page.
	 *
	 * @var array
	 */
	public $results;

	/**
	 * The total number of results.
	 *
	 * @var int
	 */
	public $total;

	/**
	 * The current page.
	 *
	 * @var int
	 */
	public $page;

	/**
	 * The number of items per page.
	 *
	 * @var int
	 */
	public $per_page;

	/**
	 * The last page available for the result set.
	 *
	 * @var int
	 */
	public $last_page;

	/**
	 * The number of links that should be adjacent to the current page.
	 *
	 * @var int
	 */
	public $adjacent = 3;

	/**
	 * Indicates if the generated links should use HTTPS.
	 *
	 * @var bool
	 */
	public $secure;

	/**
	 * The language that should be used when generating page links.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * The values that should be appended to the end of the link query strings.
	 *
	 * @var array
	 */
	public $append = array();

	/**
	 * Create a new Paginator instance.
	 *
	 * In general, the Paginator will be instantiated through the database query. However, you are free
	 * to instantiate a paginator for an arbitrary array if you wish.
	 *
	 * <code>
	 *		// Create a Paginator for the first page of 10 total results and 2 items per page
	 *		$paginator = new Paginator(1, 10, 2);
	 * </code>
	 *
	 * @param  int    $page
	 * @param  int    $total
	 * @param  int    $per_page
	 * @return void
	 */
	public function __construct($page, $total, $per_page)
	{
		$this->last_page = ceil($total / $per_page);
		$this->per_page = $per_page;
		$this->total = $total;

		// Determine if the current request is using HTTPS. If it is, we will use HTTPS when
		// generating the links unless otherwise specified by the secure() method.
		$this->secure = Request::active()->is_secure();

		// The page method will validate the given page number and adjust it if necessary.
		// For example, when the given page number is greater than the last page or less
		// than zero, the page number will be adjusted.
		$this->page = $this->adjust($page);
	}

	/**
	 * Check a given page number for validity and adjust it if necessary.
	 *
	 * The page will be validated and adjusted if it is less than one or greater than the last page.
	 * For example, if the current page is not an integer or less than one, one will be returned.
	 * If the current page is greater than the last page, the last page will be returned.
	 *
	 * @param  int    $page
	 * @return int
	 */
	private function adjust($page)
	{
		if (is_numeric($page) and $page > $this->last_page) return ($this->last_page > 0) ? $this->last_page : 1;

		return ($page < 1 or filter_var($page, FILTER_VALIDATE_INT) === false) ? 1 : $page;
	}

	/**
	 * Create the HTML pagination links.
	 *
	 * If there are enough pages, an intelligent, sliding list of links will be created. 
	 * Otherwise, a simple list of page number links will be created.
	 *
	 * @return string
	 */
	public function links()
	{
		if ($this->last_page <= 1) return '';

		// The hard-coded "7" is to account for all of the constant elements in a sliding range.
		// Namely: The the current page, the two ellipses, the two beginning pages, and the two ending pages.
		$numbers = ($this->last_page < 7 + ($this->adjacent * 2)) ? $this->range(1, $this->last_page) : $this->slider();

		return '<div class="pagination">'.$this->previous().$numbers.$this->next().'</div>';
	}

	/**
	 * Build a sliding list of HTML numeric page links.
	 *
	 * If the current page is close to the beginning of the pages, all of the beginning links will be
	 * shown and the ending links will be abbreviated.
	 *
	 * If the current page is in the middle of the pages, the beginning and ending links will be abbreviated.
	 *
	 * If the current page is close to the end of the list of pages, all of the ending links will be
	 * shown and the beginning links will be abbreviated.
	 *
	 * @return string
	 */
	private function slider()
	{
		if ($this->page <= $this->adjacent * 2)
		{
			return $this->range(1, 2 + ($this->adjacent * 2)).$this->ending();
		}
		elseif ($this->page >= $this->last_page - ($this->adjacent * 2))
		{
			return $this->beginning().$this->range($this->last_page - 2 - ($this->adjacent * 2), $this->last_page);
		}
		else
		{
			return $this->beginning().$this->range($this->page - $this->adjacent, $this->page + $this->adjacent).$this->ending();
		}
	}

	/**
	 * Generate the "previous" HTML link.
	 *
	 * The "previous" line from the "pagination" language file will be used to create the link text.
	 *
	 * @return string
	 */
	public function previous()
	{
		$text = Lang::line('pagination.previous')->get($this->language);

		if ($this->page > 1)
		{
			return $this->link($this->page - 1, $text, 'prev_page').' ';
		}

		return HTML::span($text, array('class' => 'disabled prev_page')).' ';
	}

	/**
	 * Generate the "next" HTML link.
	 *
	 * The "next" line from the "pagination" language file will be used to create the link text.
	 *
	 * @return string
	 */
	public function next()
	{
		$text = Lang::line('pagination.next')->get($this->language);

		if ($this->page < $this->last_page)
		{
			return $this->link($this->page + 1, $text, 'next_page');
		}

		return HTML::span($text, array('class' => 'disabled next_page'));
	}

	/**
	 * Build the first two page links for a sliding page range.
	 *
	 * @return string
	 */
	private function beginning()
	{
		return $this->range(1, 2).'<span class="dots">...</span>';
	}

	/**
	 * Build the last two page links for a sliding page range.
	 *
	 * @return string
	 */
	private function ending()
	{
		return '<span class="dots">...</span>'.$this->range($this->last_page - 1, $this->last_page);
	}

	/**
	 * Build a range of page links.
	 *
	 * A span element will be generated for the current page.
	 *
	 * @param  int     $start
	 * @param  int     $end
	 * @return string
	 */
	private function range($start, $end)
	{
		$pages = '';

		for ($i = $start; $i <= $end; $i++)
		{
			if ($this->page == $i)
			{
				$pages .= HTML::span($i, array('class' => 'current')).' ';
			}
			else
			{
				$pages .= $this->link($i, $i, null).' ';
			}
		}

		return $pages;
	}

	/**
	 * Create a HTML page link.
	 *
	 * @param  int     $page
	 * @param  string  $text
	 * @param  string  $attributes
	 * @return string
	 */
	private function link($page, $text, $class)
	{
		$append = '';

		foreach ($this->append as $key => $value)
		{
			$append .= '&'.$key.'='.$value;
		}

		return HTML::link(Request::active()->uri().'?page='.$page.$append, $text, compact('class'), $this->secure);
	}

	/**
	 * Force the paginator to return links that use HTTPS.
	 *
	 * @param  bool       $secure
	 * @return Paginator
	 */
	public function secure($secure = true)
	{
		$this->secure = true;
		return $this;
	}

	/**
	 * Set the language that should be used when generating page links.
	 *
	 * The language specified here should correspond to a language directory for your application.
	 *
	 * @param  string     $language
	 * @return Paginator
	 */
	public function lang($language)
	{
		$this->language = $language;
		return $this;
	}

	/**
	 * Set the items that should be appended to the link query strings.
	 *
	 * <code>
	 *		// Set the "sort" query string item on the links that will be generated
	 *		echo $paginator->append(array('sort' => 'desc'))->links();
	 * </code>
	 *
	 * @param  array      $values
	 * @return Paginator
	 */
	public function append($values)
	{
		$this->append = $values;
		return $this;
	}

}