<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;


/**
 * Paginating math.
 *
 * @property   int $page
 * @property-read int $firstPage
 * @property-read ?int $lastPage
 * @property-read int<0,max> $firstItemOnPage
 * @property-read int<0,max> $lastItemOnPage
 * @property   int $base
 * @property-read bool $first
 * @property-read bool $last
 * @property-read ?int<0,max> $pageCount
 * @property   positive-int $itemsPerPage
 * @property   ?int<0,max> $itemCount
 * @property-read int<0,max> $offset
 * @property-read ?int<0,max> $countdownOffset
 * @property-read int<0,max> $length
 */
class Paginator
{
	use Nette\SmartObject;

	private int $base = 1;

	/** @var positive-int */
	private int $itemsPerPage = 1;

	private int $page = 1;

	/** @var ?int<0, max> */
	private ?int $itemCount = null;


	public function setPage(int $page): static
	{
		$this->page = $page;
		return $this;
	}


	public function getPage(): int
	{
		return $this->base + $this->getPageIndex();
	}


	public function getFirstPage(): int
	{
		return $this->base;
	}


	public function getLastPage(): ?int
	{
		return $this->itemCount === null
			? null
			: $this->base + max(0, $this->getPageCount() - 1);
	}


	/**
	 * Returns the 1-based index of the first item on the current page, or 0 if the page is empty.
	 * @return int<0, max>
	 */
	public function getFirstItemOnPage(): int
	{
		return $this->itemCount !== 0
			? $this->offset + 1
			: 0;
	}


	/**
	 * Returns the 1-based index of the last item on the current page.
	 * @return int<0, max>
	 */
	public function getLastItemOnPage(): int
	{
		return $this->offset + $this->length;
	}


	public function setBase(int $base): static
	{
		$this->base = $base;
		return $this;
	}


	public function getBase(): int
	{
		return $this->base;
	}


	/**
	 * Returns zero-based page number.
	 * @return int<0, max>
	 */
	protected function getPageIndex(): int
	{
		$index = max(0, $this->page - $this->base);
		return $this->itemCount === null
			? $index
			: min($index, max(0, $this->getPageCount() - 1));
	}


	public function isFirst(): bool
	{
		return $this->getPageIndex() === 0;
	}


	public function isLast(): bool
	{
		return $this->itemCount === null
			? false
			: $this->getPageIndex() >= $this->getPageCount() - 1;
	}


	/**
	 * @return ?int<0, max>
	 */
	public function getPageCount(): ?int
	{
		return $this->itemCount === null
			? null
			: max(0, (int) ceil($this->itemCount / $this->itemsPerPage));
	}


	public function setItemsPerPage(int $itemsPerPage): static
	{
		$this->itemsPerPage = max(1, $itemsPerPage);
		return $this;
	}


	/**
	 * @return positive-int
	 */
	public function getItemsPerPage(): int
	{
		return $this->itemsPerPage;
	}


	public function setItemCount(?int $itemCount = null): static
	{
		$this->itemCount = $itemCount === null ? null : max(0, $itemCount);
		return $this;
	}


	/**
	 * @return ?int<0, max>
	 */
	public function getItemCount(): ?int
	{
		return $this->itemCount;
	}


	/**
	 * Returns the absolute index of the first item on current page.
	 * @return int<0, max>
	 */
	public function getOffset(): int
	{
		return $this->getPageIndex() * $this->itemsPerPage;
	}


	/**
	 * Returns the absolute index of the first item on current page in countdown paging.
	 * @return ?int<0, max>
	 */
	public function getCountdownOffset(): ?int
	{
		return $this->itemCount === null
			? null
			: max(0, $this->itemCount - ($this->getPageIndex() + 1) * $this->itemsPerPage);
	}


	/**
	 * Returns the number of items on current page.
	 * @return int<0, max>
	 */
	public function getLength(): int
	{
		return $this->itemCount === null
			? $this->itemsPerPage
			: max(0, min($this->itemsPerPage, $this->itemCount - $this->getPageIndex() * $this->itemsPerPage));
	}
}
