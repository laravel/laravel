<?php
/**
 *	@package	SimpleTest
 *	@subpackage	Extensions
 *  @author     Perrick Penet <perrick@noparking.net>
 *	@version	$Id: css_selector.php 1802 2008-09-08 10:43:58Z maetl_ $
 */

/**
 * CssSelector
 * 
 * Allow to navigate a DOM with CSS selector.
 *
 * based on getElementsBySelector version 0.4 - Simon Willison, 2003-03-25
 * http://simon.incutio.com/archive/2003/03/25/getElementsBySelector
 *
 * derived from sfDomCssSelector Id 3053 - Fabien Potencier, 2006-12-16
 * http://www.symfony-project.com/api/symfony/util/sfDomCssSelector.html
 *
 * @package SimpleTest
 * @subpackage Extensions
 * @param DomDocument $dom
 */
class CssSelector
{
  public $nodes = array();

  public function __construct($nodes)
  {
    if (!is_array($nodes))
    {
      $nodes = array($nodes);
    }

    $this->nodes = $nodes;
  }

  public function getNodes()
  {
    return $this->nodes;
  }

  public function getNode()
  {
    return $this->nodes ? $this->nodes[0] : null;
  }

  public function getValue()
  {
    return $this->nodes[0]->nodeValue;
  }

  public function getValues()
  {
    $values = array();
    foreach ($this->nodes as $node)
    {
      $values[] = $node->nodeValue;
    }

    return $values;
  }

  public function matchSingle($selector)
  {
    $nodes = $this->getElements($selector);

    return $nodes ? new CssSelector($nodes[0]) : new CssSelector(array());
  }

  public function matchAll($selector)
  {
    $nodes = $this->getElements($selector);

    return $nodes ? new CssSelector($nodes) : new CssSelector(array());
  }

  /* DEPRECATED */
  public function getTexts($selector)
  {
    $texts = array();
    foreach ($this->getElements($selector) as $element)
    {
      $texts[] = $element->nodeValue;
    }

    return $texts;
  }

  /* DEPRECATED */
  public function getElements($selector)
  {
    $nodes = array();
    foreach ($this->nodes as $node)
    {
      $result_nodes = $this->getElementsForNode($selector, $node);
      if ($result_nodes)
      {
        $nodes = array_merge($nodes, $result_nodes);
      }
    }

    foreach ($nodes as $node)
    {
      $node->removeAttribute('sf_matched');
    }

    return $nodes;
  }

  protected function getElementsForNode($selector, $root_node)
  {
    $all_nodes = array();
    foreach ($this->tokenize_selectors($selector) as $selector)
    {
      $nodes = array($root_node);
      foreach ($this->tokenize($selector) as $token)
      {
        $combinator = $token['combinator'];
        $selector = $token['selector'];

        $token = trim($token['name']);

        $pos = strpos($token, '#');
        if (false !== $pos && preg_match('/^[A-Za-z0-9]*$/', substr($token, 0, $pos)))
        {
          // Token is an ID selector
          $tagName = substr($token, 0, $pos);
          $id = substr($token, $pos + 1);
          $xpath = new DomXPath($root_node);
          $element = $xpath->query(sprintf("//*[@id = '%s']", $id))->item(0);
          if (!$element || ($tagName && strtolower($element->nodeName) != $tagName))
          {
            // tag with that ID not found
            return array();
          }

          // Set nodes to contain just this element
          $nodes = array($element);
          $nodes = $this->matchCustomSelector($nodes, $selector);

          continue; // Skip to next token
        }

        $pos = strpos($token, '.');
        if (false !== $pos && preg_match('/^[A-Za-z0-9\*]*$/', substr($token, 0, $pos)))
        {
          // Token contains a class selector
          $tagName = substr($token, 0, $pos);
          if (!$tagName)
          {
            $tagName = '*';
          }
          $className = substr($token, $pos + 1);

          // Get elements matching tag, filter them for class selector
          $founds = $this->getElementsByTagName($nodes, $tagName, $combinator);
          $nodes = array();
          foreach ($founds as $found)
          {
            if (preg_match('/\b'.$className.'\b/', $found->getAttribute('class')))
            {
              $nodes[] = $found;
            }
          }

          $nodes = $this->matchCustomSelector($nodes, $selector);

          continue; // Skip to next token
        }

        // Code to deal with attribute selectors
        if (preg_match('/^(\w+|\*)(\[.+\])$/', $token, $matches))
        {
          $tagName = $matches[1] ? $matches[1] : '*';
          preg_match_all('/
            \[
              (\w+)                 # attribute
              ([=~\|\^\$\*]?)       # modifier (optional)
              =?                    # equal (optional)
              (
                "([^"]*)"           # quoted value (optional)
                |
                ([^\]]*)            # non quoted value (optional)
              )
            \]
          /x', $matches[2], $matches, PREG_SET_ORDER);

          // Grab all of the tagName elements within current node
          $founds = $this->getElementsByTagName($nodes, $tagName, $combinator);
          $nodes = array();
          foreach ($founds as $found)
          {
            $ok = false;
            foreach ($matches as $match)
            {
              $attrName = $match[1];
              $attrOperator = $match[2];
              $attrValue = $match[4];

              switch ($attrOperator)
              {
                case '=': // Equality
                  $ok = $found->getAttribute($attrName) == $attrValue;
                  break;
                case '~': // Match one of space seperated words
                  $ok = preg_match('/\b'.preg_quote($attrValue, '/').'\b/', $found->getAttribute($attrName));
                  break;
                case '|': // Match start with value followed by optional hyphen
                  $ok = preg_match('/^'.preg_quote($attrValue, '/').'-?/', $found->getAttribute($attrName));
                  break;
                case '^': // Match starts with value
                  $ok = 0 === strpos($found->getAttribute($attrName), $attrValue);
                  break;
                case '$': // Match ends with value
                  $ok = $attrValue == substr($found->getAttribute($attrName), -strlen($attrValue));
                  break;
                case '*': // Match ends with value
                  $ok = false !== strpos($found->getAttribute($attrName), $attrValue);
                  break;
                default :
                  // Just test for existence of attribute
                  $ok = $found->hasAttribute($attrName);
              }

              if (false == $ok)
              {
                break;
              }
            }

            if ($ok)
            {
              $nodes[] = $found;
            }
          }

          continue; // Skip to next token
        }

        // If we get here, token is JUST an element (not a class or ID selector)
        $nodes = $this->getElementsByTagName($nodes, $token, $combinator);

        $nodes = $this->matchCustomSelector($nodes, $selector);
      }

      foreach ($nodes as $node)
      {
        if (!$node->getAttribute('sf_matched'))
        {
          $node->setAttribute('sf_matched', true);
          $all_nodes[] = $node;
        }
      }
    }

    return $all_nodes;
  }

  protected function getElementsByTagName($nodes, $tagName, $combinator = ' ')
  {
    $founds = array();
    foreach ($nodes as $node)
    {
      switch ($combinator)
      {
        case ' ':
          // Descendant selector
          foreach ($node->getElementsByTagName($tagName) as $element)
          {
            $founds[] = $element;
          }
          break;
        case '>':
          // Child selector
          foreach ($node->childNodes as $element)
          {
            if ($tagName == $element->nodeName)
            {
              $founds[] = $element;
            }
          }
          break;
        case '+':
          // Adjacent selector
          $element = $node->nextSibling;
          if ($element && '#text' == $element->nodeName)
          {
            $element = $element->nextSibling;
          }

          if ($element && $tagName == $element->nodeName)
          {
            $founds[] = $element;
          }
          break;
        default:
          throw new Exception(sprintf('Unrecognized combinator "%s".', $combinator));
      }
    }

    return $founds;
  }

  protected function tokenize_selectors($selector)
  {
    // split tokens by , except in an attribute selector
    $tokens = array();
    $quoted = false;
    $token = '';
    for ($i = 0, $max = strlen($selector); $i < $max; $i++)
    {
      if (',' == $selector[$i] && !$quoted)
      {
        $tokens[] = trim($token);
        $token = '';
      }
      else if ('"' == $selector[$i])
      {
        $token .= $selector[$i];
        $quoted = $quoted ? false : true;
      }
      else
      {
        $token .= $selector[$i];
      }
    }
    if ($token)
    {
      $tokens[] = trim($token);
    }

    return $tokens;
  }

  protected function tokenize($selector)
  {
    // split tokens by space except if space is in an attribute selector
    $tokens = array();
    $combinators = array(' ', '>', '+');
    $quoted = false;
    $token = array('combinator' => ' ', 'name' => '');
    for ($i = 0, $max = strlen($selector); $i < $max; $i++)
    {
      if (in_array($selector[$i], $combinators) && !$quoted)
      {
        // remove all whitespaces around the combinator
        $combinator = $selector[$i];
        while (in_array($selector[$i + 1], $combinators))
        {
          if (' ' != $selector[++$i])
          {
            $combinator = $selector[$i];
          }
        }

        $tokens[] = $token;
        $token = array('combinator' => $combinator, 'name' => '');
      }
      else if ('"' == $selector[$i])
      {
        $token['name'] .= $selector[$i];
        $quoted = $quoted ? false : true;
      }
      else
      {
        $token['name'] .= $selector[$i];
      }
    }
    if ($token['name'])
    {
      $tokens[] = $token;
    }

    foreach ($tokens as &$token)
    {
      list($token['name'], $token['selector']) = $this->tokenize_selector_name($token['name']);
    }

    return $tokens;
  }

  protected function tokenize_selector_name($token_name)
  {
    // split custom selector
    $quoted = false;
    $name = '';
    $selector = '';
    $in_selector = false;
    for ($i = 0, $max = strlen($token_name); $i < $max; $i++)
    {
      if ('"' == $token_name[$i])
      {
        $quoted = $quoted ? false : true;
      }

      if (!$quoted && ':' == $token_name[$i])
      {
        $in_selector = true;
      }

      if ($in_selector)
      {
        $selector .= $token_name[$i];
      }
      else
      {
        $name .= $token_name[$i];
      }
    }

    return array($name, $selector);
  }

  protected function matchCustomSelector($nodes, $selector)
  {
    if (!$selector)
    {
      return $nodes;
    }

    $selector = $this->tokenize_custom_selector($selector);
    $matchingNodes = array();
    for ($i = 0, $max = count($nodes); $i < $max; $i++)
    {
      switch ($selector['selector'])
      {
        case 'contains':
          if (false !== strpos($nodes[$i]->textContent, $selector['parameter']))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'nth-child':
          if ($nodes[$i] === $this->nth($nodes[$i]->parentNode->firstChild, (integer) $selector['parameter']))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'first-child':
          if ($nodes[$i] === $this->nth($nodes[$i]->parentNode->firstChild))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'last-child':
          if ($nodes[$i] === $this->nth($nodes[$i]->parentNode->lastChild, 1, 'previousSibling'))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'lt':
          if ($i < (integer) $selector['parameter'])
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'gt':
          if ($i > (integer) $selector['parameter'])
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'odd':
          if ($i % 2)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'even':
          if (0 == $i % 2)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'nth':
        case 'eq':
          if ($i == (integer) $selector['parameter'])
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'first':
          if ($i == 0)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'last':
          if ($i == $max - 1)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        default:
          throw new Exception(sprintf('Unrecognized selector "%s".', $selector['selector']));
      }
    }

    return $matchingNodes;
  }

  protected function tokenize_custom_selector($selector)
  {
    if (!preg_match('/
      ([a-zA-Z0-9\-]+)
      (?:
        \(
          (?:
            ("|\')(.*)?\2
            |
            (.*?)
          )
        \)
      )?
    /x', substr($selector, 1), $matches))
    {
      throw new Exception(sprintf('Unable to parse custom selector "%s".', $selector));
    }
    return array('selector' => $matches[1], 'parameter' => isset($matches[3]) ? ($matches[3] ? $matches[3] : $matches[4]) : '');
  }

  protected function nth($cur, $result = 1, $dir = 'nextSibling')
  {
    $num = 0;
    for (; $cur; $cur = $cur->$dir)
    {
      if (1 == $cur->nodeType)
      {
        ++$num;
      }

      if ($num == $result)
      {
        return $cur;
      }
    }
  }
}
