<?php

/* vim: set shiftwidth=2 expandtab softtabstop=2: */

/**
 * @author Rob Morris <rob@irongaze.com>
 * @author Chris Corbyn <chris@w3style.co.uk>
 *
 * Copyright © 2013 Rob Morris.
 */

namespace Boris;

/**
 * Identifies data types in data structures and syntax highlights them.
 */
class ColoredInspector implements Inspector {
  static $TERM_COLORS = array(
    'black'        => "\033[0;30m",
    'white'        => "\033[1;37m",
    'none'         => "\033[1;30m",
    'dark_grey'    => "\033[1;30m",
    'light_grey'   => "\033[0;37m",
    'dark_red'     => "\033[0;31m",
    'light_red'    => "\033[1;31m",
    'dark_green'   => "\033[0;32m",
    'light_green'  => "\033[1;32m",
    'dark_yellow'  => "\033[0;33m",
    'light_yellow' => "\033[1;33m",
    'dark_blue'    => "\033[0;34m",
    'light_blue'   => "\033[1;34m",
    'dark_purple'  => "\033[0;35m",
    'light_purple' => "\033[1;35m",
    'dark_cyan'    => "\033[0;36m",
    'light_cyan'   => "\033[1;36m",
  );

  private $_fallback;
  private $_colorMap = array();

  /**
   * Initialize a new ColoredInspector, using $colorMap.
   *
   * The colors should be an associative array with the keys:
   *
   *   - 'integer'
   *   - 'float'
   *   - 'keyword'
   *   - 'string'
   *   - 'boolean'
   *   - 'default'
   *
   * And the values, one of the following colors:
   *
   *   - 'none'
   *   - 'black'
   *   - 'white'
   *   - 'dark_grey'
   *   - 'light_grey'
   *   - 'dark_red'
   *   - 'light_red'
   *   - 'dark_green'
   *   - 'light_green'
   *   - 'dark_yellow'
   *   - 'light_yellow'
   *   - 'dark_blue'
   *   - 'light_blue'
   *   - 'dark_purple'
   *   - 'light_purple'
   *   - 'dark_cyan'
   *   - 'light_cyan'
   *
   * An empty $colorMap array effectively means 'none' for all types.
   *
   * @param array $colorMap
   */
  public function __construct($colorMap = null) {
    $this->_fallback = new DumpInspector();

    if (isset($colorMap)) {
      $this->_colorMap = $colorMap;
    } else {
      $this->_colorMap = $this->_defaultColorMap();
    }
  }

  public function inspect($variable) {
    return preg_replace(
      '/^/m',
      $this->_colorize('comment', '// '),
      $this->_dump($variable)
    );
  }

  /**
   * Returns an associative array of an object's properties.
   *
   * This method is public so that subclasses may override it.
   *
   * @param object $value
   * @return array
   * */
  public function objectVars($value) {
    return get_object_vars($value);
  }

  // -- Private Methods

  public function _dump($value) {
    $tests = array(
      'is_null'    => '_dumpNull',
      'is_string'  => '_dumpString',
      'is_bool'    => '_dumpBoolean',
      'is_integer' => '_dumpInteger',
      'is_float'   => '_dumpFloat',
      'is_array'   => '_dumpArray',
      'is_object'  => '_dumpObject'
    );

    foreach ($tests as $predicate => $outputMethod) {
      if (call_user_func($predicate, $value))
        return call_user_func(array($this, $outputMethod), $value);
    }

    return $this->_fallback->inspect($value);
  }

  private function _dumpNull($value) {
    return $this->_colorize('keyword', 'NULL');
  }

  private function _dumpString($value) {
    return $this->_colorize('string', var_export($value, true));
  }

  private function _dumpBoolean($value) {
    return $this->_colorize('bool', var_export($value, true));
  }

  private function _dumpInteger($value) {
    return $this->_colorize('integer', var_export($value, true));
  }

  private function _dumpFloat($value) {
    return $this->_colorize('float', var_export($value, true));
  }

  private function _dumpArray($value) {
    return $this->_dumpStructure('array', $value);
  }

  private function _dumpObject($value) {
    return $this->_dumpStructure(
      sprintf('object(%s)', get_class($value)),
      $this->objectVars($value)
    );
  }

  private function _dumpStructure($type, $value) {
    return $this->_astToString($this->_buildAst($type, $value));
  }

  public function _buildAst($type, $value, $seen = array()) {
    // FIXME: Improve this AST so it doesn't require access to dump() or colorize()
    if ($this->_isSeen($value, $seen)) {
      return $this->_colorize('default', '*** RECURSION ***');
    } else {
      $nextSeen = array_merge($seen, array($value));
    }

    if (is_object($value)) {
      $vars = $this->objectVars($value);
    } else {
      $vars = $value;
    }

    $self = $this;

    return array(
      'name'     => $this->_colorize('keyword', $type),
      'children' => empty($vars) ? array() : array_combine(
        array_map(array($this, '_dump'), array_keys($vars)),
        array_map(
          function($v) use($self, $nextSeen) {
            if (is_object($v)) {
              return $self->_buildAst(
                sprintf('object(%s)', get_class($v)),
                $v,
                $nextSeen
              );
            } elseif (is_array($v)) {
              return $self->_buildAst('array', $v, $nextSeen);
            } else {
              return $self->_dump($v);
            }
          },
          array_values($vars)
        )
      )
    );
  }

  public function _astToString($node, $indent = 0) {
    $children = $node['children'];
    $self     = $this;

    return implode(
      "\n",
      array(
        sprintf('%s(', $node['name']),
        implode(
          ",\n",
          array_map(
            function($k) use($self, $children, $indent) {
              if (is_array($children[$k])) {
                return sprintf(
                  '%s%s => %s',
                  str_repeat(' ', ($indent + 1) * 2),
                  $k,
                  $self->_astToString($children[$k], $indent + 1)
                );
              } else {
                return sprintf(
                  '%s%s => %s',
                  str_repeat(' ', ($indent + 1) * 2),
                  $k,
                  $children[$k]
                );
              }
            },
            array_keys($children)
          )
        ),
        sprintf('%s)', str_repeat(' ', $indent * 2))
      )
    );
  }

  private function _defaultColorMap() {
    return array(
      'integer' => 'light_green',
      'float'   => 'light_yellow',
      'string'  => 'light_red',
      'bool'    => 'light_purple',
      'keyword' => 'light_cyan',
      'comment' => 'dark_grey',
      'default' => 'none'
    );
  }

  private function _colorize($type, $value) {
    if (!empty($this->_colorMap[$type])) {
      $colorName = $this->_colorMap[$type];
    } else {
      $colorName = $this->_colorMap['default'];
    }

    return sprintf(
      "%s%s\033[0m",
      static::$TERM_COLORS[$colorName],
      $value
    );
  }

  private function _isSeen($value, $seen) {
    foreach ($seen as $v) {
      if ($v === $value)
        return true;
    }

    return false;
  }
}
