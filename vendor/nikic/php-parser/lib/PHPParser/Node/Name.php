<?php

/**
 * @property array $parts Parts of the name
 */
class PHPParser_Node_Name extends PHPParser_NodeAbstract
{
    /**
     * Constructs a name node.
     *
     * @param string|array $parts      Parts of the name (or name as string)
     * @param array        $attributes Additional attributes
     */
    public function __construct($parts, array $attributes = array()) {
        if (!is_array($parts)) {
            $parts = explode('\\', $parts);
        }

        parent::__construct(
            array(
                'parts' => $parts,
            ),
            $attributes
        );
    }

    /**
     * Gets the first part of the name, i.e. everything before the first namespace separator.
     *
     * @return string First part of the name
     */
    public function getFirst() {
        return $this->parts[0];
    }

    /**
     * Gets the last part of the name, i.e. everything after the last namespace separator.
     *
     * @return string Last part of the name
     */
    public function getLast() {
        return $this->parts[count($this->parts) - 1];
    }

    /**
     * Checks whether the name is unqualified. (E.g. Name)
     *
     * @return bool Whether the name is unqualified
     */
    public function isUnqualified() {
        return 1 == count($this->parts);
    }

    /**
     * Checks whether the name is qualified. (E.g. Name\Name)
     *
     * @return bool Whether the name is qualified
     */
    public function isQualified() {
        return 1 < count($this->parts);
    }

    /**
     * Checks whether the name is fully qualified. (E.g. \Name)
     *
     * @return bool Whether the name is fully qualified
     */
    public function isFullyQualified() {
        return false;
    }

    /**
     * Checks whether the name is explicitly relative to the current namespace. (E.g. namespace\Name)
     *
     * @return bool Whether the name is relative
     */
    public function isRelative() {
        return false;
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with a separator.
     *
     * @param string $separator The separator to use (defaults to the namespace separator \)
     *
     * @return string String representation
     */
    public function toString($separator = '\\') {
        return implode($separator, $this->parts);
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with the
     * namespace separator.
     *
     * @return string String representation
     */
    public function __toString() {
        return implode('\\', $this->parts);
    }

    /**
     * Sets the whole name.
     *
     * @param string|array|self $name The name to set the whole name to
     */
    public function set($name) {
        $this->parts = $this->prepareName($name);
    }

    /**
     * Prepends a name to this name.
     *
     * @param string|array|self $name Name to prepend
     */
    public function prepend($name) {
        $this->parts = array_merge($this->prepareName($name), $this->parts);
    }

    /**
     * Appends a name to this name.
     *
     * @param string|array|self $name Name to append
     */
    public function append($name) {
        $this->parts = array_merge($this->parts, $this->prepareName($name));
    }

    /**
     * Sets the first part of the name.
     *
     * @param string|array|self $name The name to set the first part to
     */
    public function setFirst($name) {
        array_splice($this->parts, 0, 1, $this->prepareName($name));
    }

    /**
     * Sets the last part of the name.
     *
     * @param string|array|self $name The name to set the last part to
     */
    public function setLast($name) {
        array_splice($this->parts, -1, 1, $this->prepareName($name));
    }

    /**
     * Prepares a (string, array or Name node) name for use in name changing methods by converting
     * it to an array.
     *
     * @param string|array|self $name Name to prepare
     *
     * @return array Prepared name
     */
    protected function prepareName($name) {
        if (is_string($name)) {
            return explode('\\', $name);
        } elseif (is_array($name)) {
            return $name;
        } elseif ($name instanceof self) {
            return $name->parts;
        }

        throw new InvalidArgumentException(
            'When changing a name you need to pass either a string, an array or a Name node'
        );
    }
}