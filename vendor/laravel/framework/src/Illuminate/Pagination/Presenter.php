<?php namespace Illuminate\Pagination;

abstract class Presenter {

	/**
	 * The paginator instance being rendered.
	 *
	 * @var \Illuminate\Pagination\Paginator
	 */
	protected $paginator;

	/**
	 * The current page of the paginator.
	 *
	 * @var int
	 */
	protected $currentPage;

	/**
	 * The last available page of the paginator.
	 *
	 * @var int
	 */
	protected $lastPage;

	/**
	 * Create a new Presenter instance.
	 *
	 * @param  \Illuminate\Pagination\Paginator  $paginator
	 * @return void
	 */
	public function __construct(Paginator $paginator)
	{
		$this->paginator = $paginator;
		$this->lastPage = $this->paginator->getLastPage();
		$this->currentPage = $this->paginator->getCurrentPage();
	}

	/**
	 * Get HTML wrapper for a page link.
	 *
	 * @param  string  $url
	 * @param  int  $page
	 * @return string
	 */
	abstract public function getPageLinkWrapper($url, $page);

	/**
	 * Get HTML wrapper for disabled text.
	 *
	 * @param  string  $text
	 * @return string
	 */
	abstract public function getDisabledTextWrapper($text);

	/**
	 * Get HTML wrapper for active text.
	 *
	 * @param  string  $text
	 * @return string
	 */
	abstract public function getActivePageWrapper($text);

	/**
	 * Render the Pagination contents.
	 *
	 * @return string
	 */
	public function render()
	{
		// The hard-coded thirteen represents the minimum number of pages we need to
		// be able to create a sliding page window. If we have less than that, we
		// will just render a simple range of page links insteadof the sliding.
		if ($this->lastPage < 13)
		{
			$content = $this->getPageRange(1, $this->lastPage);
		}
		else
		{
			$content = $this->getPageSlider();
		}

		return $this->getPrevious().$content.$this->getNext();
	}

	/**
	 * Create a range of pagination links.
	 *
	 * @param  int  $start
	 * @param  int  $end
	 * @return string
	 */
	public function getPageRange($start, $end)
	{
		$pages = array();

		for ($page = $start; $page <= $end; $page++)
		{
			// If the current page is equal to the page we're iterating on, we will create a
			// disabled link for that page. Otherwise, we can create a typical active one
			// for the link. We will use this implementing class's methods to get HTML.
			if ($this->currentPage == $page)
			{
				$pages[] = $this->getActivePageWrapper($page);
			}
			else
			{
				$pages[] = $this->getLink($page);
			}
		}

		return implode('', $pages);
	}

	/**
	 * Create a pagination slider link window.
	 *
	 * @return string
	 */
	protected function getPageSlider()
	{
		$window = 6;

		// If the current page is very close to the beginning of the page range, we will
		// just render the beginning of the page range, followed by the last 2 of the
		// links in this list, since we will not have room to create a full slider.
		if ($this->currentPage <= $window)
		{
			$ending = $this->getFinish();

			return $this->getPageRange(1, $window + 2).$ending;
		}

		// If the current page is close to the ending of the page range we will just get
		// this first couple pages, followed by a larger window of these ending pages
		// since we're too close to the end of the list to create a full on slider.
		elseif ($this->currentPage >= $this->lastPage - $window)
		{
			$start = $this->lastPage - 8;

			$content = $this->getPageRange($start, $this->lastPage);

			return $this->getStart().$content;
		}

		// If we have enough room on both sides of the current page to build a slider we
		// will surround it with both the beginning and ending caps, with this window
		// of pages in the middle providing a Google style sliding paginator setup.
		else
		{
			$content = $this->getAdjacentRange();

			return $this->getStart().$content.$this->getFinish();
		}
	}

	/**
	 * Get the page range for the current page window.
	 *
	 * @return string
	 */
	public function getAdjacentRange()
	{
		return $this->getPageRange($this->currentPage - 3, $this->currentPage + 3);
	}

	/**
	 * Create the beginning leader of a pagination slider.
	 *
	 * @return string
	 */
	public function getStart()
	{
		return $this->getPageRange(1, 2).$this->getDots();
	}

	/**
	 * Create the ending cap of a pagination slider.
	 *
	 * @return string
	 */
	public function getFinish()
	{
		$content = $this->getPageRange($this->lastPage - 1, $this->lastPage);

		return $this->getDots().$content;
	}

	/**
	 * Get the previous page pagination element.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function getPrevious($text = '&laquo;')
	{
		// If the current page is less than or equal to one, it means we can't go any
		// further back in the pages, so we will render a disabled previous button
		// when that is the case. Otherwise, we will give it an active "status".
		if ($this->currentPage <= 1)
		{
			return $this->getDisabledTextWrapper($text);
		}
		else
		{
			$url = $this->paginator->getUrl($this->currentPage - 1);

			return $this->getPageLinkWrapper($url, $text);
		}
	}

	/**
	 * Get the next page pagination element.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function getNext($text = '&raquo;')
	{
		// If the current page is greater than or equal to the last page, it means we
		// can't go any further into the pages, as we're already on this last page
		// that is available, so we will make it the "next" link style disabled.
		if ($this->currentPage >= $this->lastPage)
		{
			return $this->getDisabledTextWrapper($text);
		}
		else
		{
			$url = $this->paginator->getUrl($this->currentPage + 1);

			return $this->getPageLinkWrapper($url, $text);
		}
	}

	/**
	 * Get a pagination "dot" element.
	 *
	 * @return string
	 */
	public function getDots()
	{
		return $this->getDisabledTextWrapper("...");
	}

	/**
	 * Create a pagination slider link.
	 *
	 * @param  mixed   $page
	 * @return string
	 */
	public function getLink($page)
	{
		$url = $this->paginator->getUrl($page);

		return $this->getPageLinkWrapper($url, $page);
	}

	/**
	 * Set the value of the current page.
	 *
	 * @param  int   $page
	 * @return void
	 */
	public function setCurrentPage($page)
	{
		$this->currentPage = $page;
	}

	/**
	 * Set the value of the last page.
	 *
	 * @param  int   $page
	 * @return void
	 */
	public function setLastPage($page)
	{
		$this->lastPage = $page;
	}

}
