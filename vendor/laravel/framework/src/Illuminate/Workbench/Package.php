<?php namespace Illuminate\Workbench;

class Package {

	/**
	 * The vendor name of the package.
	 *
	 * @var string
	 */
	public $vendor;

	/**
	 * The snake-cased version of the vendor.
	 *
	 * @var string
	 */
	public $lowerVendor;

	/**
	 * The name of the package.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The snake-cased version of the package.
	 *
	 * @var string
	 */
	public $lowerName;

	/**
	 * The name of the author.
	 *
	 * @var string
	 */
	public $author;

	/**
	 * The email address of the author.
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Create a new package instance.
	 *
	 * @param  string  $vendor
	 * @param  string  $name
	 * @param  string  $author
	 * @param  string  $email
	 * @return void
	 */
	public function __construct($vendor, $name, $author, $email)
	{
		$this->name = $name;
		$this->email = $email;
		$this->vendor = $vendor;
		$this->author = $author;
		$this->lowerName = snake_case($name, '-');
		$this->lowerVendor = snake_case($vendor, '-');
	}

	/**
	 * Get the full package name.
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return $this->lowerVendor.'/'.$this->lowerName;
	}

}
