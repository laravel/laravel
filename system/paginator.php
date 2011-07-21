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
	 * The last page number.
	 *
	 * @var int
	 */
	public $last_page;

	/**
	 * Create a new Paginator instance.
	 *
	 * @param  array  $results
	 * @param  int    $total
	 * @param  int    $per_page
	 * @return void
	 */
	public function __construct($results, $total, $per_page)
	{
		$this->per_page = $per_page;
		$this->results = $results;
		$this->total = $total;

		$this->page = static::page($this->last_page());
	}

	/**
	 * Get the current page from the request query string.
	 *
	 * The page will be validated and adjusted if it is less than 1 or
	 * greater than the last page number.
	 *
	 * @param  int  $last_page
	 * @return int
	 */
	public static function page($last_page)
	{
		$page = Input::get('page', 1);

		if (is_numeric($page) and $page > $last_page)
		{
			return $last_page;
		}

		return (filter_var($page, FILTER_VALIDATE_INT) === false or $page < 1) ? 1 : $page;
	}

	/**
	 * Create the HTML pagination links.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	public function links($adjacent = 3)
	{
		if ($this->last_page() > 1)
		{
			return '<div class="pagination">'.$this->previous().$this->numbers($adjacent).$this->next();
		}

		return '';
	}

	/**
	 * Generate the HTML numeric page links.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	public function numbers($adjacent = 3)
	{
		// If there are not enough pages to make it worth sliding, we will show all of the pages.
		//
		// We add "7" for the constant elements in a slider: the first and last two links, the
		// current page, and the two "..." strings.
		return ($this->last_page() < 7 + ($adjacent * 2)) ? $this->range(1, $this->last_page()) : $this->slider($adjacent);
	}

	/**
	 * Build sliding list of HTML numeric page links.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	protected function slider($adjacent)
	{
		$pagination = '';

		if ($this->page <= $adjacent * 2)
		{
			// Buffer the current page with four pages to the right. Any more pages will interfere with hiding.
			$pagination .= $this->range(1, 4 + ($adjacent * 2)).$this->ending();
		}
		elseif ($this->page >= $this->last_page() - ($adjacent * 2))
		{
			// Buffer with at least two pages to the left of the current page.
			$pagination .= $this->beginning().$this->range($this->last_page() - 2 - ($adjacent * 2), $this->last_page());
		}
		else
		{
			$pagination .= $this->beginning().$this->range($this->page - $adjacent, $this->page + $adjacent).$this->ending();
		}

		return $pagination;
	}

	/**
	 * Generate the "previous" HTML link.
	 *
	 * @param  string  $language
	 * @return string
	 */
	public function previous($language = null)
	{
		$text = Lang::line('pagination.previous')->get($language);

		if ($this->page > 1)
		{
			return HTML::link(Request::uri().'?page='.($this->page - 1), $text, array('class' => 'prev_page')).' ';
		}

		return HTML::span($text, array('class' => 'disabled prev_page')).' ';
	}

	/**
	 * Generate the "next" HTML link.
	 *
	 * @param  string  $language
	 * @return string
	 */
	public function next($language = null)
	{
		$text = Lang::line('pagination.next')->get($language);

		if ($this->page < $this->last_page())
		{
			return HTML::link(Request::uri().'?page='.($this->page + 1), $text, array('class' => 'next_page'));
		}

		return HTML::span($text, array('class' => 'disabled next_page'));
	}

	/**
	 * Build the first two page links for a sliding page range.
	 *
	 * @return string
	 */
	protected function beginning()
	{
		return $this->range(1, 2).' ... ';
	}

	/**
	 * Build the last two page links for a sliding page range.
	 *
	 * @return string
	 */
	protected function ending()
	{
		return ' ... '.$this->range($this->last_page() - 1, $this->last_page());
	}

	/**
	 * Calculate the last page based on the last page and the items per page.
	 *
	 * @return int
	 */
	protected function last_page()
	{
		return ceil($this->total / $this->per_page);
	}

	/**
	 * Build a range of page links. 
	 *
	 * For the current page, an HTML span element will be generated instead of a link.
	 *
	 * @param  int  $start
	 * @param  int  $end
	 * @return string
	 */
	protected function range($start, $end)
	{
		$pages = '';

		for ($i = $start; $i <= $end; $i++)
		{
			$pages .= ($this->page == $i) ? HTML::span($i, array('class' => 'current')).' ' : HTML::link(Request::uri().'?page='.$i, $i).' ';
		}

		return $pages;
	}

}