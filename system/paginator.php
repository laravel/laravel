<?php namespace System;

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
	 * The language that should be used when generating page links.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * Create a new Paginator instance.
	 *
	 * @param  array  $results
	 * @param  int    $page
	 * @param  int    $total
	 * @param  int    $per_page
	 * @param  int    $last_page
	 * @return void
	 */
	public function __construct($results, $page, $total, $per_page, $last_page)
	{
		$this->last_page = $last_page;
		$this->per_page = $per_page;
		$this->results = $results;
		$this->total = $total;
		$this->page = $page;
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
		return new static($results, static::page($total, $per_page), $total, $per_page, ceil($total / $per_page));
	}

	/**
	 * Get the current page from the request query string.
	 *
	 * The page will be validated and adjusted if it is less than one or greater than the last page.
	 * For example, if the current page is not an integer or less than one, one will be returned.
	 * If the current page is greater than the last page, the last page will be returned.
	 *
	 * @param  int  $total
	 * @param  int  $per_page
	 * @return int
	 */
	public static function page($total, $per_page)
	{
		$page = Input::get('page', 1);

		if (is_numeric($page) and $page > $last_page = ceil($total / $per_page))
		{
			return ($last_page > 0) ? $last_page : 1;
		}

		return ($page < 1 or filter_var($page, FILTER_VALIDATE_INT) === false) ? 1 : $page;
	}

	/**
	 * Create the HTML pagination links.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	public function links($adjacent = 3)
	{
		if ($this->last_page <= 1) return '';

		return '<div class="pagination">'.$this->previous().$this->numbers($adjacent).$this->next().'</div>';
	}

	/**
	 * Generate the HTML numeric page links.
	 *
	 * If there are not enough pages to make it worth sliding, all of the pages will be listed.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	private function numbers($adjacent = 3)
	{
		// The hard-coded "7" is to account for all of the constant elements in a sliding range.
		// Namely: The the current page, the two ellipses, the two beginning pages, and the two ending pages.
		if ($this->last_page < 7 + ($adjacent * 2))
		{
			return $this->range(1, $this->last_page);
		}

		return $this->slider($adjacent);
	}

	/**
	 * Build sliding list of HTML numeric page links.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	private function slider($adjacent)
	{
		if ($this->page <= $adjacent * 2)
		{
			return $this->range(1, 2 + ($adjacent * 2)).$this->ending();
		}
		elseif ($this->page >= $this->last_page - ($adjacent * 2))
		{
			return $this->beginning().$this->range($this->last_page - 2 - ($adjacent * 2), $this->last_page);
		}

		return $this->beginning().$this->range($this->page - $adjacent, $this->page + $adjacent).$this->ending();
	}

	/**
	 * Generate the "previous" HTML link.
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
	 * For the current page, an HTML span element will be generated instead of a link.
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
			$pages .= ($this->page == $i) ? HTML::span($i, array('class' => 'current')).' ' : $this->link($i, $i, null).' ';
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
		return HTML::link(Request::uri().'?page='.$page, $text, array('class' => $class), Request::is_secure());
	}

	/**
	 * Set the language that should be used when generating page links.
	 *
	 * @param  string     $language
	 * @return Paginator
	 */
	public function lang($language)
	{
		$this->language = $language;
		return $this;
	}

}