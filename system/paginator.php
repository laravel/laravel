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
		$this->page = static::page($total, $per_page);

		$this->per_page = $per_page;
		$this->results = $results;
		$this->total = $total;

		$this->last_page = ceil($total / $per_page);
	}

	/**
	 * Get the current page from the request query string.
	 *
	 * The page will be validated and adjusted if it is less than one or greater than the last page.
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
		return ($this->last_page > 1) ? '<div class="pagination">'.$this->previous().$this->numbers($adjacent).$this->next() : '';
	}

	/**
	 * Generate the HTML numeric page links.
	 *
	 * If there are not enough pages to make it worth sliding, all of the pages will be listed.
	 *
	 * Note: "7" is added to the adjacent range to account for the seven constant elements in a
	 *       slider: the first and last two links, the current page, and the two "..." strings.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	private function numbers($adjacent = 3)
	{
		return ($this->last_page < 7 + ($adjacent * 2)) ? $this->range(1, $this->last_page) : $this->slider($adjacent);
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
			return $this->range(1, 4 + ($adjacent * 2)).$this->ending();
		}
		elseif ($this->page >= $this->last_page - ($adjacent * 2))
		{
			return $this->beginning().$this->range($this->last_page - 2 - ($adjacent * 2), $this->last_page);
		}
		else
		{
			return $this->beginning().$this->range($this->page - $adjacent, $this->page + $adjacent).$this->ending();
		}
	}

	/**
	 * Generate the "previous" HTML link.
	 *
	 * @return string
	 */
	public function previous()
	{
		$text = Lang::line('pagination.previous')->get();

		if ($this->page > 1)
		{
			return HTML::link(Request::uri().'?page='.($this->page - 1), $text, array('class' => 'prev_page')).' ';
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
		$text = Lang::line('pagination.next')->get();

		if ($this->page < $this->last_page)
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
	 * @param  int  $start
	 * @param  int  $end
	 * @return string
	 */
	private function range($start, $end)
	{
		$pages = '';

		for ($i = $start; $i <= $end; $i++)
		{
			$pages .= ($this->page == $i) ? HTML::span($i, array('class' => 'current')).' ' : HTML::link(Request::uri().'?page='.$i, $i).' ';
		}

		return $pages;
	}

}