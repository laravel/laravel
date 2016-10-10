<?php namespace Illuminate\Pagination;

class BootstrapPresenter extends Presenter {

	/**
	 * Get HTML wrapper for a page link.
	 *
	 * @param  string  $url
	 * @param  int  $page
	 * @return string
	 */
	public function getPageLinkWrapper($url, $page)
	{
		return '<li><a href="'.$url.'">'.$page.'</a></li>';
	}

	/**
	 * Get HTML wrapper for disabled text.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function getDisabledTextWrapper($text)
	{
		return '<li class="disabled"><span>'.$text.'</span></li>';
	}

	/**
	 * Get HTML wrapper for active text.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function getActivePageWrapper($text)
	{
		return '<li class="active"><span>'.$text.'</span></li>';
	}

}
