<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette\HtmlStringable;
use function array_merge, array_splice, count, explode, func_num_args, html_entity_decode, htmlspecialchars, http_build_query, implode, is_array, is_bool, is_float, is_object, is_string, json_encode, max, number_format, rtrim, str_contains, str_repeat, str_replace, strip_tags, strncmp, strpbrk, substr, trigger_error, ucfirst;
use const E_USER_DEPRECATED, ENT_HTML5, ENT_NOQUOTES, ENT_QUOTES;


/**
 * Generates HTML elements with automatic attribute escaping.
 *
 * @property ?string $accept
 * @property ?string $accesskey
 * @property ?string $action
 * @property ?string $align
 * @property ?string $allow
 * @property ?string $alt
 * @property ?bool   $async
 * @property ?string $autocapitalize
 * @property ?string $autocomplete
 * @property ?bool   $autofocus
 * @property ?bool   $autoplay
 * @property ?string $charset
 * @property ?bool   $checked
 * @property ?string $cite
 * @property ?string $class
 * @property ?int    $cols
 * @property ?int    $colspan
 * @property ?string $content
 * @property ?bool   $contenteditable
 * @property ?bool   $controls
 * @property ?string $coords
 * @property ?string $crossorigin
 * @property ?string $data
 * @property ?string $datetime
 * @property ?string $decoding
 * @property ?bool   $default
 * @property ?bool   $defer
 * @property ?string $dir
 * @property ?string $dirname
 * @property ?bool   $disabled
 * @property ?bool   $download
 * @property ?string $draggable
 * @property ?string $dropzone
 * @property ?string $enctype
 * @property ?string $for
 * @property ?string $form
 * @property ?string $formaction
 * @property ?string $formenctype
 * @property ?string $formmethod
 * @property ?bool   $formnovalidate
 * @property ?string $formtarget
 * @property ?string $headers
 * @property ?int    $height
 * @property ?bool   $hidden
 * @property ?float  $high
 * @property ?string $href
 * @property ?string $hreflang
 * @property ?string $id
 * @property ?string $integrity
 * @property ?string $inputmode
 * @property ?bool   $ismap
 * @property ?string $itemprop
 * @property ?string $kind
 * @property ?string $label
 * @property ?string $lang
 * @property ?string $list
 * @property ?bool   $loop
 * @property ?float  $low
 * @property ?float  $max
 * @property ?int    $maxlength
 * @property ?int    $minlength
 * @property ?string $media
 * @property ?string $method
 * @property ?float  $min
 * @property ?bool   $multiple
 * @property ?bool   $muted
 * @property ?string $name
 * @property ?bool   $novalidate
 * @property ?bool   $open
 * @property ?float  $optimum
 * @property ?string $pattern
 * @property ?string $ping
 * @property ?string $placeholder
 * @property ?string $poster
 * @property ?string $preload
 * @property ?string $radiogroup
 * @property ?bool   $readonly
 * @property ?string $rel
 * @property ?bool   $required
 * @property ?bool   $reversed
 * @property ?int    $rows
 * @property ?int    $rowspan
 * @property ?string $sandbox
 * @property ?string $scope
 * @property ?bool   $selected
 * @property ?string $shape
 * @property ?int    $size
 * @property ?string $sizes
 * @property ?string $slot
 * @property ?int    $span
 * @property ?string $spellcheck
 * @property ?string $src
 * @property ?string $srcdoc
 * @property ?string $srclang
 * @property ?string $srcset
 * @property ?int    $start
 * @property ?float  $step
 * @property ?string $style
 * @property ?int    $tabindex
 * @property ?string $target
 * @property ?string $title
 * @property ?string $translate
 * @property ?string $type
 * @property ?string $usemap
 * @property ?string $value
 * @property ?int    $width
 * @property ?string $wrap
 *
 * @method self accept(?string $val)
 * @method self accesskey(?string $val, bool $state = null)
 * @method self action(?string $val)
 * @method self align(?string $val)
 * @method self allow(?string $val, bool $state = null)
 * @method self alt(?string $val)
 * @method self async(?bool $val)
 * @method self autocapitalize(?string $val)
 * @method self autocomplete(?string $val)
 * @method self autofocus(?bool $val)
 * @method self autoplay(?bool $val)
 * @method self charset(?string $val)
 * @method self checked(?bool $val)
 * @method self cite(?string $val)
 * @method self class(?string $val, bool $state = null)
 * @method self cols(?int $val)
 * @method self colspan(?int $val)
 * @method self content(?string $val)
 * @method self contenteditable(?bool $val)
 * @method self controls(?bool $val)
 * @method self coords(?string $val)
 * @method self crossorigin(?string $val)
 * @method self datetime(?string $val)
 * @method self decoding(?string $val)
 * @method self default(?bool $val)
 * @method self defer(?bool $val)
 * @method self dir(?string $val)
 * @method self dirname(?string $val)
 * @method self disabled(?bool $val)
 * @method self download(?bool $val)
 * @method self draggable(?string $val)
 * @method self dropzone(?string $val)
 * @method self enctype(?string $val)
 * @method self for(?string $val)
 * @method self form(?string $val)
 * @method self formaction(?string $val)
 * @method self formenctype(?string $val)
 * @method self formmethod(?string $val)
 * @method self formnovalidate(?bool $val)
 * @method self formtarget(?string $val)
 * @method self headers(?string $val, bool $state = null)
 * @method self height(?int $val)
 * @method self hidden(?bool $val)
 * @method self high(?float $val)
 * @method self hreflang(?string $val)
 * @method self id(?string $val)
 * @method self integrity(?string $val)
 * @method self inputmode(?string $val)
 * @method self ismap(?bool $val)
 * @method self itemprop(?string $val)
 * @method self kind(?string $val)
 * @method self label(?string $val)
 * @method self lang(?string $val)
 * @method self list(?string $val)
 * @method self loop(?bool $val)
 * @method self low(?float $val)
 * @method self max(?float $val)
 * @method self maxlength(?int $val)
 * @method self minlength(?int $val)
 * @method self media(?string $val)
 * @method self method(?string $val)
 * @method self min(?float $val)
 * @method self multiple(?bool $val)
 * @method self muted(?bool $val)
 * @method self name(?string $val)
 * @method self novalidate(?bool $val)
 * @method self open(?bool $val)
 * @method self optimum(?float $val)
 * @method self pattern(?string $val)
 * @method self ping(?string $val, bool $state = null)
 * @method self placeholder(?string $val)
 * @method self poster(?string $val)
 * @method self preload(?string $val)
 * @method self radiogroup(?string $val)
 * @method self readonly(?bool $val)
 * @method self rel(?string $val)
 * @method self required(?bool $val)
 * @method self reversed(?bool $val)
 * @method self rows(?int $val)
 * @method self rowspan(?int $val)
 * @method self sandbox(?string $val, bool $state = null)
 * @method self scope(?string $val)
 * @method self selected(?bool $val)
 * @method self shape(?string $val)
 * @method self size(?int $val)
 * @method self sizes(?string $val)
 * @method self slot(?string $val)
 * @method self span(?int $val)
 * @method self spellcheck(?string $val)
 * @method self src(?string $val)
 * @method self srcdoc(?string $val)
 * @method self srclang(?string $val)
 * @method self srcset(?string $val)
 * @method self start(?int $val)
 * @method self step(?float $val)
 * @method self style(?string $property, string $val = null)
 * @method self tabindex(?int $val)
 * @method self target(?string $val)
 * @method self title(?string $val)
 * @method self translate(?string $val)
 * @method self type(?string $val)
 * @method self usemap(?string $val)
 * @method self value(?string $val)
 * @method self width(?int $val)
 * @method self wrap(?string $val)
 *
 * @method static static text(mixed $text)
 * @method static static html(mixed $html)
 *
 * @implements \IteratorAggregate<int, self|string>
 * @implements \ArrayAccess<int, self|string>
 */
class Html implements \ArrayAccess, \Countable, \IteratorAggregate, HtmlStringable
{
	/** @var array<string, mixed>  element's attributes */
	public array $attrs = [];

	/** @var array<string, int>  void elements */
	public static array $emptyElements = [
		'img' => 1, 'hr' => 1, 'br' => 1, 'input' => 1, 'meta' => 1, 'area' => 1, 'embed' => 1, 'keygen' => 1,
		'source' => 1, 'base' => 1, 'col' => 1, 'link' => 1, 'param' => 1, 'basefont' => 1, 'frame' => 1,
		'isindex' => 1, 'wbr' => 1, 'command' => 1, 'track' => 1,
	];

	/** @var array<int, self|string> nodes */
	protected array $children = [];

	/** element's name */
	private string $name = '';

	private bool $isEmpty = false;


	/**
	 * Constructs new HTML element.
	 * @param  array<string, mixed>|string|null  $attrs element's attributes or plain text content
	 */
	public static function el(?string $name = null, array|string|null $attrs = null): static
	{
		$el = new static;
		$parts = explode(' ', (string) $name, 2);
		$el->setName($parts[0]);

		if (is_array($attrs)) {
			$el->attrs = $attrs;

		} elseif ($attrs !== null) {
			$el->setText($attrs);
		}

		if (isset($parts[1])) {
			foreach (Strings::matchAll($parts[1] . ' ', '#([a-z0-9:-]+)(?:=(["\'])?(.*?)(?(2)\2|\s))?#i') as $m) {
				$el->attrs[$m[1]] = $m[3] ?? true;
			}
		}

		return $el;
	}


	/**
	 * Creates a nameless element (fragment) containing the given children.
	 * Everything except HtmlStringable is escaped; use Html::html() for raw HTML. Nulls are skipped.
	 */
	public static function fragment(HtmlStringable|\Stringable|string|int|null ...$children): static
	{
		return (new static)->add(...$children);
	}


	/**
	 * Returns an object representing HTML text.
	 * @deprecated  use Html::html()
	 */
	public static function fromHtml(string $html): static
	{
		return (new static)->setHtml($html);
	}


	/**
	 * Returns an object representing plain text.
	 * @deprecated  use Html::text()
	 */
	public static function fromText(string $text): static
	{
		return (new static)->setText($text);
	}


	/**
	 * Converts to HTML.
	 */
	final public function toHtml(): string
	{
		return $this->render();
	}


	/**
	 * Converts to plain text.
	 */
	final public function toText(): string
	{
		return $this->getText();
	}


	/**
	 * Converts given HTML code to plain text.
	 */
	public static function htmlToText(string $html): string
	{
		return html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}


	/**
	 * Changes element's name.
	 */
	final public function setName(string $name, ?bool $isEmpty = null): static
	{
		$this->name = $name;
		$this->isEmpty = $isEmpty ?? isset(static::$emptyElements[$name]);
		return $this;
	}


	/**
	 * Returns element's name.
	 */
	final public function getName(): string
	{
		return $this->name;
	}


	/**
	 * Checks whether the element is a void (self-closing) element.
	 */
	final public function isEmpty(): bool
	{
		return $this->isEmpty;
	}


	/**
	 * Sets multiple attributes.
	 * @param  array<string, mixed>  $attrs
	 */
	public function addAttributes(array $attrs): static
	{
		$this->attrs = array_merge($this->attrs, $attrs);
		return $this;
	}


	/**
	 * Appends value to element's attribute.
	 */
	public function appendAttribute(string $name, mixed $value, mixed $option = true): static
	{
		if (is_array($value)) {
			$prev = isset($this->attrs[$name]) ? (array) $this->attrs[$name] : [];
			$this->attrs[$name] = $value + $prev;

		} elseif ((string) $value === '') {
			$tmp = &$this->attrs[$name]; // appending empty value? -> ignore, but ensure it exists

		} elseif (!isset($this->attrs[$name]) || is_array($this->attrs[$name])) { // needs array
			$this->attrs[$name][$value] = $option;

		} else {
			$this->attrs[$name] = [$this->attrs[$name] => true, $value => $option];
		}

		return $this;
	}


	/**
	 * Sets element's attribute.
	 */
	public function setAttribute(string $name, mixed $value): static
	{
		$this->attrs[$name] = $value;
		return $this;
	}


	/**
	 * Returns element's attribute.
	 */
	public function getAttribute(string $name): mixed
	{
		return $this->attrs[$name] ?? null;
	}


	/**
	 * Unsets element's attribute.
	 */
	public function removeAttribute(string $name): static
	{
		unset($this->attrs[$name]);
		return $this;
	}


	/**
	 * Unsets element's attributes.
	 * @param  list<string>  $attributes
	 */
	public function removeAttributes(array $attributes): static
	{
		foreach ($attributes as $name) {
			unset($this->attrs[$name]);
		}

		return $this;
	}


	/**
	 * Sets element's attribute via property assignment.
	 */
	final public function __set(string $name, mixed $value): void
	{
		$this->attrs[$name] = $value;
	}


	/**
	 * Returns element's attribute via property access.
	 */
	final public function &__get(string $name): mixed
	{
		return $this->attrs[$name];
	}


	/**
	 * Checks if element's attribute is set.
	 */
	final public function __isset(string $name): bool
	{
		return isset($this->attrs[$name]);
	}


	/**
	 * Unsets element's attribute via property unset.
	 */
	final public function __unset(string $name): void
	{
		unset($this->attrs[$name]);
	}


	/**
	 * Sets or returns element's attribute via method call.
	 * @param  mixed[]  $args
	 */
	final public function __call(string $m, array $args): mixed
	{
		if ($m === 'text' || $m === 'html') {
			trigger_error("Method \$el->$m() is deprecated, use set" . ucfirst($m) . "() for content or setAttribute() for the '$m' attribute; Html::$m() is a static factory.", E_USER_DEPRECATED);
		}

		$p = substr($m, 0, 3);
		if ($p === 'get' || $p === 'set' || $p === 'add') {
			$m = substr($m, 3);
			$m[0] = $m[0] | "\x20";
			if ($p === 'get') {
				return $this->attrs[$m] ?? null;

			} elseif ($p === 'add') {
				$args[] = true;
			}
		}

		if (count($args) === 0) { // invalid

		} elseif (count($args) === 1) { // set
			$this->attrs[$m] = $args[0];

		} else { // add
			$this->appendAttribute($m, $args[0], $args[1]);
		}

		return $this;
	}


	/**
	 * Creates element with escaped text (Html::text()) or raw HTML (Html::html()) content.
	 * @param  mixed[]  $args
	 */
	final public static function __callStatic(string $name, array $args): static
	{
		return match ($name) {
			'text' => (new static)->setText(...$args),
			'html' => (new static)->setHtml(...$args),
			default => ObjectHelpers::strictStaticCall(static::class, $name),
		};
	}


	/**
	 * Special setter for element's attribute.
	 * @param  array<string, mixed>  $query
	 */
	final public function href(string $path, array $query = []): static
	{
		if ($query) {
			$query = http_build_query($query, '', '&');
			if ($query !== '') {
				$path .= '?' . $query;
			}
		}

		$this->attrs['href'] = $path;
		return $this;
	}


	/**
	 * Setter for data-* attributes. Booleans are converted to 'true' resp. 'false'.
	 */
	public function data(string $name, mixed $value = null): static
	{
		if (func_num_args() === 1) {
			$this->attrs['data'] = $name;
		} else {
			$this->attrs["data-$name"] = is_bool($value)
				? json_encode($value)
				: $value;
		}

		return $this;
	}


	/**
	 * Sets element's HTML content.
	 */
	final public function setHtml(mixed $html): static
	{
		$this->children = [(string) $html];
		return $this;
	}


	/**
	 * Returns element's HTML content.
	 */
	final public function getHtml(): string
	{
		return implode('', $this->children);
	}


	/**
	 * Sets element's textual content.
	 */
	final public function setText(mixed $text): static
	{
		if (!$text instanceof HtmlStringable) {
			$text = htmlspecialchars((string) $text, ENT_NOQUOTES, 'UTF-8');
		}

		$this->children = [(string) $text];
		return $this;
	}


	/**
	 * Returns element's textual content.
	 */
	final public function getText(): string
	{
		return self::htmlToText($this->getHtml());
	}


	/**
	 * Appends the given children. Everything except HtmlStringable is escaped; use Html::html() for raw HTML. Nulls are skipped.
	 */
	public function add(HtmlStringable|\Stringable|string|int|null ...$children): static
	{
		foreach ($children as $child) {
			if ($child !== null) {
				$this->addText($child);
			}
		}

		return $this;
	}


	/**
	 * Adds new element's child.
	 */
	final public function addHtml(HtmlStringable|string $child): static
	{
		return $this->insert(null, $child);
	}


	/**
	 * Appends plain-text string to element content.
	 */
	public function addText(\Stringable|string|int|null $text): static
	{
		if (!$text instanceof HtmlStringable) {
			$text = htmlspecialchars((string) $text, ENT_NOQUOTES, 'UTF-8');
		}

		return $this->insert(null, $text);
	}


	/**
	 * Creates and adds a new Html child.
	 * @param  array<string, mixed>|string|null  $attrs
	 */
	final public function create(string $name, array|string|null $attrs = null): static
	{
		$this->insert(null, $child = static::el($name, $attrs));
		return $child;
	}


	/**
	 * Inserts child node.
	 */
	public function insert(?int $index, HtmlStringable|string $child, bool $replace = false): static
	{
		$child = $child instanceof self ? $child : (string) $child;
		if ($index === null) { // append
			$this->children[] = $child;

		} else { // insert or replace
			array_splice($this->children, $index, $replace ? 1 : 0, [$child]);
		}

		return $this;
	}


	/**
	 * Inserts (replaces) child node (\ArrayAccess implementation).
	 * @param  ?int  $index  position or null for appending
	 * @param  Html|string  $child  Html node or raw HTML string
	 */
	final public function offsetSet($index, $child): void
	{
		$this->insert($index, $child, replace: true);
	}


	/**
	 * Returns child node (\ArrayAccess implementation).
	 * @param  int  $index
	 */
	final public function offsetGet($index): self|string
	{
		return $this->children[$index];
	}


	/**
	 * Exists child node? (\ArrayAccess implementation).
	 * @param  int  $index
	 */
	final public function offsetExists($index): bool
	{
		return isset($this->children[$index]);
	}


	/**
	 * Removes child node (\ArrayAccess implementation).
	 * @param  int  $index
	 */
	public function offsetUnset($index): void
	{
		if (isset($this->children[$index])) {
			array_splice($this->children, $index, 1);
		}
	}


	/**
	 * Returns children count.
	 */
	final public function count(): int
	{
		return count($this->children);
	}


	/**
	 * Removes all children.
	 */
	public function removeChildren(): void
	{
		$this->children = [];
	}


	/**
	 * Iterates over elements.
	 * @return \ArrayIterator<int, self|string>
	 */
	final public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->children);
	}


	/**
	 * Returns all children.
	 * @return array<int, self|string>
	 */
	final public function getChildren(): array
	{
		return $this->children;
	}


	/**
	 * Renders element's start tag, content and end tag. Pass indent level to enable pretty-printing.
	 */
	final public function render(?int $indent = null): string
	{
		$s = $this->startTag();

		if (!$this->isEmpty) {
			// add content
			if ($indent !== null) {
				$indent++;
			}

			foreach ($this->children as $child) {
				if ($child instanceof self) {
					$s .= $child->render($indent);
				} else {
					$s .= $child;
				}
			}

			// add end tag
			$s .= $this->endTag();
		}

		if ($indent !== null) {
			return "\n" . str_repeat("\t", $indent - 1) . $s . "\n" . str_repeat("\t", max(0, $indent - 2));
		}

		return $s;
	}


	final public function __toString(): string
	{
		return $this->render();
	}


	/**
	 * Returns element's start tag.
	 */
	final public function startTag(): string
	{
		return $this->name
			? '<' . $this->name . $this->attributes() . '>'
			: '';
	}


	/**
	 * Returns element's end tag.
	 */
	final public function endTag(): string
	{
		return $this->name && !$this->isEmpty ? '</' . $this->name . '>' : '';
	}


	/**
	 * Returns element's attributes.
	 * @internal
	 */
	final public function attributes(): string
	{
		$s = '';
		$attrs = $this->attrs;
		foreach ($attrs as $key => $value) {
			if ($value === null || $value === false) {
				continue;

			} elseif ($value === true) {
				$s .= ' ' . $key;

				continue;

			} elseif (is_array($value)) {
				if (str_starts_with($key, 'data-')) {
					$value = Json::encode($value);

				} else {
					$tmp = null;
					foreach ($value as $k => $v) {
						if ($v != null) { // intentionally ==, skip nulls & empty string
							// composite 'style' vs. 'others'
							$tmp[] = $v === true
								? $k
								: (is_string($k) ? $k . ':' . $v : $v);
						}
					}

					if ($tmp === null) {
						continue;
					}

					$value = implode($key === 'style' || !strncmp($key, 'on', 2) ? ';' : ' ', $tmp);
				}
			} elseif (is_float($value)) {
				$value = rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');

			} else {
				$value = (string) $value;
			}

			$q = str_contains($value, '"') ? "'" : '"';
			$s .= ' ' . $key . '=' . $q
				. str_replace(
					['&', $q],
					['&amp;', $q === '"' ? '&quot;' : '&#39;'],
					$value,
				)
				. (str_contains($value, '`') && strpbrk($value, ' <>"\'') === false ? ' ' : '')
				. $q;
		}

		$s = str_replace('@', '&#64;', $s);
		return $s;
	}


	/**
	 * Clones all children too.
	 */
	public function __clone()
	{
		foreach ($this->children as $key => $value) {
			if (is_object($value)) {
				$this->children[$key] = clone $value;
			}
		}
	}
}
