<?php

namespace Laravel\SerializableClosure\Support;

use Closure;
use ReflectionFunction;

class ReflectionClosure extends ReflectionFunction
{
    protected $code;
    protected $tokens;
    protected $hashedName;
    protected $useVariables;
    protected $isStaticClosure;
    protected $isScopeRequired;
    protected $isBindingRequired;
    protected $isShortClosure;

    protected static $files = [];
    protected static $classes = [];
    protected static $functions = [];
    protected static $constants = [];
    protected static $structures = [];

    /**
     * Creates a new reflection closure instance.
     *
     * @param  \Closure  $closure
     * @param  string|null  $code
     * @return void
     */
    public function __construct(Closure $closure, $code = null)
    {
        parent::__construct($closure);
    }

    /**
     * Checks if the closure is "static".
     *
     * @return bool
     */
    public function isStatic(): bool
    {
        if ($this->isStaticClosure === null) {
            $this->isStaticClosure = strtolower(substr($this->getCode(), 0, 6)) === 'static';
        }

        return $this->isStaticClosure;
    }

    /**
     * Checks if the closure is a "short closure".
     *
     * @return bool
     */
    public function isShortClosure()
    {
        if ($this->isShortClosure === null) {
            $code = $this->getCode();

            if ($this->isStatic()) {
                $code = substr($code, 6);
            }

            $this->isShortClosure = strtolower(substr(trim($code), 0, 2)) === 'fn';
        }

        return $this->isShortClosure;
    }

    /**
     * Get the closure's code.
     *
     * @return string
     */
    public function getCode()
    {
        if ($this->code !== null) {
            return $this->code;
        }

        $fileName = $this->getFileName();
        $line = $this->getStartLine() - 1;

        $className = null;

        if (null !== $className = $this->getClosureScopeClass()) {
            $className = '\\'.trim($className->getName(), '\\');
        }

        $builtin_types = self::getBuiltinTypes();
        $class_keywords = ['self', 'static', 'parent'];

        $ns = $this->getClosureNamespaceName();
        $nsf = $ns == '' ? '' : ($ns[0] == '\\' ? $ns : '\\'.$ns);

        $_file = var_export($fileName, true);
        $_dir = var_export(dirname($fileName), true);
        $_namespace = var_export($ns, true);
        $_class = var_export(trim($className ?: '', '\\'), true);
        $_function = $ns.($ns == '' ? '' : '\\').'{closure}';
        $_method = ($className == '' ? '' : trim($className, '\\').'::').$_function;
        $_function = var_export($_function, true);
        $_method = var_export($_method, true);
        $_trait = null;

        $tokens = $this->getTokens();
        $state = $lastState = 'start';
        $inside_structure = false;
        $isFirstClassCallable = false;
        $isShortClosure = false;

        $inside_structure_mark = 0;
        $open = 0;
        $code = '';
        $id_start = $id_start_ci = $id_name = $context = '';
        $classes = $functions = $constants = null;
        $use = [];
        $lineAdd = 0;
        $isUsingScope = false;
        $isUsingThisObject = false;

        $closureArgsInnerFuncCount = 0;
        $closureArgsBraceDepth = 0;

        $candidates = [];

        for ($i = 0, $l = count($tokens); $i < $l; $i++) {
            $token = $tokens[$i];

            switch ($state) {
                case 'start':
                    if ($token[0] === T_FUNCTION || $token[0] === T_STATIC) {
                        $code .= $token[1];

                        $state = $token[0] === T_FUNCTION ? 'function' : 'static';
                    } elseif ($token[0] === T_FN) {
                        $isShortClosure = true;
                        $code .= $token[1];
                        $state = 'closure_args';
                    } elseif ($token[0] === T_PUBLIC || $token[0] === T_PROTECTED || $token[0] === T_PRIVATE) {
                        $code = '';
                        $isFirstClassCallable = true;
                    }
                    break;
                case 'static':
                    if ($token[0] === T_WHITESPACE || $token[0] === T_COMMENT || $token[0] === T_FUNCTION) {
                        $code .= $token[1];
                        if ($token[0] === T_FUNCTION) {
                            $state = 'function';
                        }
                    } elseif ($token[0] === T_FN) {
                        $isShortClosure = true;
                        $code .= $token[1];
                        $state = 'closure_args';
                    } else {
                        $code = '';
                        $state = 'start';
                    }
                    break;
                case 'function':
                    switch ($token[0]) {
                        case T_STRING:
                            if ($isFirstClassCallable) {
                                $state = 'closure_args';
                                break;
                            }

                            $code = '';
                            $state = 'named_function';
                            break;
                        case '(':
                            $code .= '(';
                            $state = 'closure_args';
                            break;
                        default:
                            $code .= is_array($token) ? $token[1] : $token;
                    }
                    break;
                case 'named_function':
                    if ($token[0] === T_FUNCTION || $token[0] === T_STATIC) {
                        $code = $token[1];
                        $state = $token[0] === T_FUNCTION ? 'function' : 'static';
                    } elseif ($token[0] === T_FN) {
                        $isShortClosure = true;
                        $code .= $token[1];
                        $state = 'closure_args';
                    }
                    break;
                case 'closure_args':
                    $insideClosureArgsInner = $closureArgsBraceDepth > 0 || $closureArgsInnerFuncCount > 0;

                    if ($insideClosureArgsInner) {
                        switch ($token[0]) {
                            case T_FUNCTION:
                                $closureArgsInnerFuncCount++;
                                $code .= $token[1];
                                break;
                            case T_CURLY_OPEN:
                            case T_DOLLAR_OPEN_CURLY_BRACES:
                                $closureArgsBraceDepth++;
                                $code .= $token[1];
                                break;
                            case '{':
                                if ($closureArgsInnerFuncCount > 0) {
                                    $closureArgsInnerFuncCount--;
                                }
                                $closureArgsBraceDepth++;
                                $code .= '{';
                                break;
                            case '}':
                                $closureArgsBraceDepth--;
                                $code .= '}';
                                break;
                            default:
                                $code .= is_array($token) ? $token[1] : $token;
                        }
                        break;
                    }

                    switch ($token[0]) {
                        case T_FUNCTION:
                            $closureArgsInnerFuncCount++;
                            $code .= $token[1];
                            break;
                        case T_NAME_QUALIFIED:
                            [$id_start, $id_start_ci, $id_name] = $this->parseNameQualified($token[1]);
                            $context = 'args';
                            $state = 'id_name';
                            $lastState = 'closure_args';
                            break;
                        case T_NS_SEPARATOR:
                        case T_STRING:
                            $id_start = $token[1];
                            $id_start_ci = strtolower($id_start);
                            $id_name = '';
                            $context = 'args';
                            $state = 'id_name';
                            $lastState = 'closure_args';
                            break;
                        case T_USE:
                            $code .= $token[1];
                            $state = 'use';
                            break;
                        case T_DOUBLE_ARROW:
                            $code .= $token[1];
                            if ($isShortClosure) {
                                $state = 'closure';
                            }
                            break;
                        case ':':
                            $code .= ':';
                            $state = 'return';
                            break;
                        case '{':
                            $code .= '{';
                            $state = 'closure';
                            $open++;
                            break;
                        default:
                            $code .= is_array($token) ? $token[1] : $token;
                    }
                    break;
                case 'use':
                    switch ($token[0]) {
                        case T_VARIABLE:
                            $use[] = substr($token[1], 1);
                            $code .= $token[1];
                            break;
                        case '{':
                            $code .= '{';
                            $state = 'closure';
                            $open++;
                            break;
                        case ':':
                            $code .= ':';
                            $state = 'return';
                            break;
                        default:
                            $code .= is_array($token) ? $token[1] : $token;
                            break;
                    }
                    break;
                case 'return':
                    switch ($token[0]) {
                        case T_WHITESPACE:
                        case T_COMMENT:
                        case T_DOC_COMMENT:
                            $code .= $token[1];
                            break;
                        case T_NS_SEPARATOR:
                        case T_STRING:
                            $id_start = $token[1];
                            $id_start_ci = strtolower($id_start);
                            $id_name = '';
                            $context = 'return_type';
                            $state = 'id_name';
                            $lastState = 'return';
                            break 2;
                        case T_NAME_QUALIFIED:
                            [$id_start, $id_start_ci, $id_name] = $this->parseNameQualified($token[1]);
                            $context = 'return_type';
                            $state = 'id_name';
                            $lastState = 'return';
                            break 2;
                        case T_DOUBLE_ARROW:
                            $code .= $token[1];
                            if ($isShortClosure) {
                                $state = 'closure';
                            }
                            break;
                        case '{':
                            $code .= '{';
                            $state = 'closure';
                            $open++;
                            break;
                        default:
                            $code .= is_array($token) ? $token[1] : $token;
                            break;
                    }
                    break;
                case 'closure':
                    switch ($token[0]) {
                        case T_CURLY_OPEN:
                        case T_DOLLAR_OPEN_CURLY_BRACES:
                        case '{':
                            $code .= is_array($token) ? $token[1] : $token;
                            $open++;
                            break;
                        case '}':
                            $code .= '}';
                            if (--$open === 0 && ! $isShortClosure) {
                                $reset = $this->collectCandidate($candidates, $code, $use, $isShortClosure, $isUsingThisObject, $isUsingScope);
                                $code = $reset['code'];
                                $state = $reset['state'];
                                $open = $reset['open'];
                                $use = $reset['use'];
                                $isShortClosure = $reset['isShortClosure'];
                                $isUsingThisObject = $reset['isUsingThisObject'];
                                $isUsingScope = $reset['isUsingScope'];
                                $closureArgsInnerFuncCount = $reset['closureArgsInnerFuncCount'];
                                $closureArgsBraceDepth = $reset['closureArgsBraceDepth'];
                            } elseif ($inside_structure) {
                                $inside_structure = ! ($open === $inside_structure_mark);
                            }
                            break;
                        case '(':
                        case '[':
                            $code .= $token[0];
                            if ($isShortClosure) {
                                $open++;
                            }
                            break;
                        case ')':
                        case ']':
                            if ($isShortClosure) {
                                if ($open === 0) {
                                    $reset = $this->collectCandidate($candidates, $code, $use, $isShortClosure, $isUsingThisObject, $isUsingScope);
                                    $code = $reset['code'];
                                    $state = $reset['state'];
                                    $open = $reset['open'];
                                    $use = $reset['use'];
                                    $isShortClosure = $reset['isShortClosure'];
                                    $isUsingThisObject = $reset['isUsingThisObject'];
                                    $isUsingScope = $reset['isUsingScope'];
                                    $closureArgsInnerFuncCount = $reset['closureArgsInnerFuncCount'];
                                    $closureArgsBraceDepth = $reset['closureArgsBraceDepth'];
                                    continue 3;
                                }
                                $open--;
                            }
                            $code .= $token[0];
                            break;
                        case ',':
                        case ';':
                            if ($isShortClosure && $open === 0) {
                                $reset = $this->collectCandidate($candidates, $code, $use, $isShortClosure, $isUsingThisObject, $isUsingScope);
                                $code = $reset['code'];
                                $state = $reset['state'];
                                $open = $reset['open'];
                                $use = $reset['use'];
                                $isShortClosure = $reset['isShortClosure'];
                                $isUsingThisObject = $reset['isUsingThisObject'];
                                $isUsingScope = $reset['isUsingScope'];
                                $closureArgsInnerFuncCount = $reset['closureArgsInnerFuncCount'];
                                $closureArgsBraceDepth = $reset['closureArgsBraceDepth'];
                                continue 3;
                            }
                            $code .= $token[0];
                            break;
                        case T_LINE:
                            $code .= $token[2] - $line + $lineAdd;
                            break;
                        case T_FILE:
                            $code .= $_file;
                            break;
                        case T_DIR:
                            $code .= $_dir;
                            break;
                        case T_NS_C:
                            $code .= $_namespace;
                            break;
                        case T_CLASS_C:
                            $code .= $inside_structure ? $token[1] : $_class;
                            break;
                        case T_FUNC_C:
                            $code .= $inside_structure ? $token[1] : $_function;
                            break;
                        case T_METHOD_C:
                            $code .= $inside_structure ? $token[1] : $_method;
                            break;
                        case T_COMMENT:
                            if (substr($token[1], 0, 8) === '#trackme') {
                                $timestamp = time();
                                $code .= '/**'.PHP_EOL;
                                $code .= '* Date      : '.date(DATE_W3C, $timestamp).PHP_EOL;
                                $code .= '* Timestamp : '.$timestamp.PHP_EOL;
                                $code .= '* Line      : '.($line + 1).PHP_EOL;
                                $code .= '* File      : '.$_file.PHP_EOL.'*/'.PHP_EOL;
                                $lineAdd += 5;
                            } else {
                                $code .= $token[1];
                            }
                            break;
                        case T_VARIABLE:
                            if ($token[1] == '$this' && ! $inside_structure) {
                                $isUsingThisObject = true;
                            }
                            $code .= $token[1];
                            break;
                        case T_STATIC:
                        case T_NS_SEPARATOR:
                        case T_STRING:
                            $id_start = $token[1];
                            $id_start_ci = strtolower($id_start);
                            $id_name = '';
                            $context = 'root';
                            $state = 'id_name';
                            $lastState = 'closure';
                            break 2;
                        case T_NAME_QUALIFIED:
                            [$id_start, $id_start_ci, $id_name] = $this->parseNameQualified($token[1]);
                            $context = 'root';
                            $state = 'id_name';
                            $lastState = 'closure';
                            break 2;
                        case T_NEW:
                            $code .= $token[1];
                            $context = 'new';
                            $state = 'id_start';
                            $lastState = 'closure';
                            break 2;
                        case T_USE:
                            $code .= $token[1];
                            $context = 'use';
                            $state = 'id_start';
                            $lastState = 'closure';
                            break;
                        case T_INSTANCEOF:
                        case T_INSTEADOF:
                            $code .= $token[1];
                            $context = 'instanceof';
                            $state = 'id_start';
                            $lastState = 'closure';
                            break;
                        case T_OBJECT_OPERATOR:
                        case T_NULLSAFE_OBJECT_OPERATOR:
                        case T_DOUBLE_COLON:
                            $code .= $token[1];
                            $lastState = 'closure';
                            $state = 'ignore_next';
                            break;
                        case T_FUNCTION:
                            $code .= $token[1];
                            $state = 'closure_args';
                            if (! $inside_structure) {
                                $inside_structure = true;
                                $inside_structure_mark = $open;
                            }
                            break;
                        case T_TRAIT_C:
                            if ($_trait === null) {
                                $startLine = $this->getStartLine();
                                $endLine = $this->getEndLine();
                                $structures = $this->getStructures();

                                $_trait = '';

                                foreach ($structures as &$struct) {
                                    if ($struct['type'] === 'trait' &&
                                        $struct['start'] <= $startLine &&
                                        $struct['end'] >= $endLine
                                    ) {
                                        $_trait = ($ns == '' ? '' : $ns.'\\').$struct['name'];
                                        break;
                                    }
                                }

                                $_trait = var_export($_trait, true);
                            }

                            $code .= $_trait;
                            break;
                        default:
                            $code .= is_array($token) ? $token[1] : $token;
                    }
                    break;
                case 'ignore_next':
                    switch ($token[0]) {
                        case T_WHITESPACE:
                        case T_COMMENT:
                        case T_DOC_COMMENT:
                            $code .= $token[1];
                            break;
                        case T_CLASS:
                        case T_NEW:
                        case T_STATIC:
                        case T_VARIABLE:
                        case T_STRING:
                        case T_CLASS_C:
                        case T_FILE:
                        case T_DIR:
                        case T_METHOD_C:
                        case T_FUNC_C:
                        case T_FUNCTION:
                        case T_INSTANCEOF:
                        case T_LINE:
                        case T_NS_C:
                        case T_TRAIT_C:
                        case T_USE:
                            $code .= $token[1];
                            $state = $lastState;
                            break;
                        default:
                            $state = $lastState;
                            $i--;
                    }
                    break;
                case 'id_start':
                    switch ($token[0]) {
                        case T_WHITESPACE:
                        case T_COMMENT:
                        case T_DOC_COMMENT:
                            $code .= $token[1];
                            break;
                        case T_NS_SEPARATOR:
                        case T_NAME_FULLY_QUALIFIED:
                        case T_STRING:
                        case T_STATIC:
                            $id_start = $token[1];
                            $id_start_ci = strtolower($id_start);
                            $id_name = '';
                            $state = 'id_name';
                            break 2;
                        case T_NAME_QUALIFIED:
                            [$id_start, $id_start_ci, $id_name] = $this->parseNameQualified($token[1]);
                            $state = 'id_name';
                            break 2;
                        case T_VARIABLE:
                            $code .= $token[1];
                            $state = $lastState;
                            break;
                        case T_CLASS:
                            $code .= $token[1];
                            $state = 'anonymous';
                            break;
                        case '(':
                            if ($context === 'instanceof') {
                                $code .= '(';
                                if ($isShortClosure) {
                                    $open++;
                                }
                                $state = $lastState;
                                break;
                            }
                            // no break
                        default:
                            $i--; //reprocess last
                            $state = 'id_name';
                    }
                    break;
                case 'id_name':
                    switch ($token[0]) {
                        case $token[0] === ':' && ! in_array($context, ['instanceof', 'new'], true):
                            if ($lastState === 'closure' && $context === 'root') {
                                $state = 'closure';
                                $code .= $id_start.$token;
                            }

                            break;
                        case T_NAME_QUALIFIED:
                        case T_NS_SEPARATOR:
                        case T_STRING:
                        case T_WHITESPACE:
                        case T_COMMENT:
                        case T_DOC_COMMENT:
                            $id_name .= $token[1];
                            break;
                        case '(':
                            if ($isShortClosure) {
                                $open++;
                            }
                            if ($context === 'new' || false !== strpos($id_name, '\\')) {
                                if ($id_start_ci === 'self' || $id_start_ci === 'static') {
                                    if (! $inside_structure) {
                                        $isUsingScope = true;
                                    }
                                } elseif ($id_start !== '\\' && ! in_array($id_start_ci, $class_keywords)) {
                                    if ($classes === null) {
                                        $classes = $this->getClasses();
                                    }
                                    if (isset($classes[$id_start_ci])) {
                                        $id_start = $classes[$id_start_ci];
                                    }
                                    if ($id_start[0] !== '\\') {
                                        $id_start = $nsf.'\\'.$id_start;
                                    }
                                }
                            } else {
                                if ($id_start !== '\\') {
                                    if ($functions === null) {
                                        $functions = $this->getFunctions();
                                    }
                                    if (isset($functions[$id_start_ci])) {
                                        $id_start = $functions[$id_start_ci];
                                    } elseif ($nsf !== '\\' && function_exists($nsf.'\\'.$id_start)) {
                                        $id_start = $nsf.'\\'.$id_start;
                                        // Cache it to functions array
                                        $functions[$id_start_ci] = $id_start;
                                    }
                                }
                            }
                            $code .= $id_start.$id_name.'(';
                            $state = $lastState;
                            break;
                        case T_VARIABLE:
                        case T_DOUBLE_COLON:
                            if ($id_start !== '\\') {
                                if ($id_start_ci === 'self' || $id_start_ci === 'parent') {
                                    if (! $inside_structure) {
                                        $isUsingScope = true;
                                    }
                                } elseif ($id_start_ci === 'static') {
                                    if (! $inside_structure) {
                                        $isUsingScope = $token[0] === T_DOUBLE_COLON;
                                    }
                                } elseif (! in_array($id_start_ci, $builtin_types)) {
                                    if ($classes === null) {
                                        $classes = $this->getClasses();
                                    }
                                    if (isset($classes[$id_start_ci])) {
                                        $id_start = $classes[$id_start_ci];
                                    }
                                    if ($id_start[0] !== '\\') {
                                        $id_start = $nsf.'\\'.$id_start;
                                    }
                                }
                            }

                            $code .= $id_start.$id_name.$token[1];
                            $state = $token[0] === T_DOUBLE_COLON ? 'ignore_next' : $lastState;
                            break;
                        default:
                            if ($id_start !== '\\' && ! defined($id_start)) {
                                if ($constants === null) {
                                    $constants = $this->getConstants();
                                }
                                if (isset($constants[$id_start])) {
                                    $id_start = $constants[$id_start];
                                } elseif ($context === 'new') {
                                    if (in_array($id_start_ci, $class_keywords)) {
                                        if (! $inside_structure) {
                                            $isUsingScope = true;
                                        }
                                    } else {
                                        if ($classes === null) {
                                            $classes = $this->getClasses();
                                        }
                                        if (isset($classes[$id_start_ci])) {
                                            $id_start = $classes[$id_start_ci];
                                        }
                                        if ($id_start[0] !== '\\') {
                                            $id_start = $nsf.'\\'.$id_start;
                                        }
                                    }
                                } elseif ($context === 'use' ||
                                    $context === 'instanceof' ||
                                    $context === 'args' ||
                                    $context === 'return_type' ||
                                    $context === 'extends' ||
                                    $context === 'root'
                                ) {
                                    if (in_array($id_start_ci, $class_keywords)) {
                                        if (! $inside_structure && $id_start_ci !== 'static') {
                                            $isUsingScope = true;
                                        }
                                    } elseif (! in_array($id_start_ci, $builtin_types)) {
                                        if ($classes === null) {
                                            $classes = $this->getClasses();
                                        }
                                        if (isset($classes[$id_start_ci])) {
                                            $id_start = $classes[$id_start_ci];
                                        }
                                        if ($id_start[0] !== '\\') {
                                            $id_start = $nsf.'\\'.$id_start;
                                        }
                                    }
                                }
                            }
                            $code .= $id_start.$id_name;
                            $state = $lastState;
                            $i--; //reprocess last token
                    }
                    break;
                case 'anonymous':
                    switch ($token[0]) {
                        case T_NAME_QUALIFIED:
                            [$id_start, $id_start_ci, $id_name] = $this->parseNameQualified($token[1]);
                            $state = 'id_name';
                            $lastState = 'anonymous';
                            break 2;
                        case T_NS_SEPARATOR:
                        case T_STRING:
                            $id_start = $token[1];
                            $id_start_ci = strtolower($id_start);
                            $id_name = '';
                            $state = 'id_name';
                            $context = 'extends';
                            $lastState = 'anonymous';
                            break;
                        case '{':
                            $state = 'closure';
                            if (! $inside_structure) {
                                $inside_structure = true;
                                $inside_structure_mark = $open;
                            }
                            $i--;
                            break;
                        default:
                            $code .= is_array($token) ? $token[1] : $token;
                    }
                    break;
            }
        }

        $attributesCode = array_values(array_filter(array_map(function ($attribute) {
            $name = $attribute->getName();

            // Skip attributes that cannot target functions. When a closure is
            // created from a method (e.g. `$obj->method(...)`), the method's
            // attributes are inherited. Attributes that only target methods
            // (like #[\Override]) would cause a fatal error when applied to
            // the serialized closure function.
            if (class_exists($name)) {
                $ref = new \ReflectionClass($name);
                $attrAttributes = $ref->getAttributes(\Attribute::class);

                if (! empty($attrAttributes)) {
                    $flags = $attrAttributes[0]->getArguments()[0] ?? \Attribute::TARGET_ALL;
                    if (($flags & \Attribute::TARGET_FUNCTION) === 0) {
                        return null;
                    }
                }
            }

            $arguments = $attribute->getArguments();
            $arguments = implode(', ', array_map(function ($argument, $key) {
                $argument = var_export($argument, true);

                if (is_string($key)) {
                    $argument = sprintf('%s: %s', $key, $argument);
                }

                return $argument;
            }, $arguments, array_keys($arguments)));

            return "#[$name($arguments)]";
        }, $this->getAttributes())));

        if (count($candidates) > 1) {
            $lastItem = array_pop($candidates);

            foreach ($candidates as $candidate) {
                if (! $this->verifyCandidateSignature($candidate)) {
                    continue;
                }

                $this->applyCandidate($candidate);

                $code = $candidate['code'];

                if (! empty($attributesCode)) {
                    $code = implode("\n", array_merge($attributesCode, [$code]));
                }

                $this->code = $code;

                return $this->code;
            }

            $candidates[] = $lastItem;
        }

        $lastItem = array_pop($candidates);

        if ($lastItem) {
            $this->applyCandidate($lastItem);
            $code = $lastItem['code'];
        } else {
            if ($isShortClosure) {
                $this->useVariables = $this->getStaticVariables();
            } else {
                $this->useVariables = empty($use) ? $use : array_intersect_key($this->getStaticVariables(), array_flip($use));
            }

            $this->isShortClosure = $isShortClosure;
            $this->isBindingRequired = $isUsingThisObject;
            $this->isScopeRequired = $isUsingScope;
        }

        if (! empty($attributesCode)) {
            $code = implode("\n", array_merge($attributesCode, [$code]));
        }

        $this->code = $code;

        return $this->code;
    }

    /**
     * Get PHP native built in types.
     *
     * @return array
     */
    protected static function getBuiltinTypes()
    {
        return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void', 'object', 'mixed', 'false', 'null', 'never', 'true'];
    }

    /**
     * Gets the use variables by the closure.
     *
     * @return array
     */
    public function getUseVariables()
    {
        if ($this->useVariables !== null) {
            return $this->useVariables;
        }

        if ($this->isShortClosure()) {
            return $this->useVariables;
        }

        $tokens = $this->getTokens();
        $use = [];
        $state = 'start';

        foreach ($tokens as &$token) {
            $is_array = is_array($token);

            switch ($state) {
                case 'start':
                    if ($is_array && $token[0] === T_USE) {
                        $state = 'use';
                    }
                    break;
                case 'use':
                    if ($is_array) {
                        if ($token[0] === T_VARIABLE) {
                            $use[] = substr($token[1], 1);
                        }
                    } elseif ($token == ')') {
                        break 2;
                    }
                    break;
            }
        }

        $this->useVariables = empty($use) ? $use : array_intersect_key($this->getStaticVariables(), array_flip($use));

        return $this->useVariables;
    }

    /**
     * Checks if binding is required.
     *
     * @return bool
     */
    public function isBindingRequired()
    {
        if ($this->isBindingRequired === null) {
            $this->getCode();
        }

        return $this->isBindingRequired;
    }

    /**
     * Checks if access to the scope is required.
     *
     * @return bool
     */
    public function isScopeRequired()
    {
        if ($this->isScopeRequired === null) {
            $this->getCode();
        }

        return $this->isScopeRequired;
    }

    /**
     * The hash of the current file name.
     *
     * @return string
     */
    protected function getHashedFileName()
    {
        if ($this->hashedName === null) {
            $this->hashedName = sha1($this->getFileName());
        }

        return $this->hashedName;
    }

    /**
     * Get the file tokens.
     *
     * @return array
     */
    protected function getFileTokens()
    {
        $key = $this->getHashedFileName();

        if (! isset(static::$files[$key])) {
            static::$files[$key] = token_get_all(file_get_contents($this->getFileName()));
        }

        return static::$files[$key];
    }

    /**
     * Get the tokens.
     *
     * @return array
     */
    protected function getTokens()
    {
        if ($this->tokens === null) {
            $tokens = $this->getFileTokens();
            $startLine = $this->getStartLine();
            $endLine = $this->getEndLine();
            $results = [];
            $start = false;

            foreach ($tokens as &$token) {
                if (! is_array($token)) {
                    if ($start) {
                        $results[] = $token;
                    }

                    continue;
                }

                $line = $token[2];

                if ($line <= $endLine) {
                    if ($line >= $startLine) {
                        $start = true;
                        $results[] = $token;
                    }

                    continue;
                }

                break;
            }

            $this->tokens = $results;
        }

        return $this->tokens;
    }

    /**
     * Get the classes.
     *
     * @return array
     */
    protected function getClasses()
    {
        $line = $this->getStartLine();

        foreach ($this->getStructures() as $struct) {
            if ($struct['type'] === 'namespace' &&
                $struct['start'] <= $line &&
                $struct['end'] >= $line
            ) {
                return $struct['classes'];
            }
        }

        return [];
    }

    /**
     * Get the functions.
     *
     * @return array
     */
    protected function getFunctions()
    {
        $key = $this->getHashedFileName();

        if (! isset(static::$functions[$key])) {
            $this->fetchItems();
        }

        return static::$functions[$key];
    }

    /**
     * Gets the constants.
     *
     * @return array
     */
    protected function getConstants()
    {
        $key = $this->getHashedFileName();

        if (! isset(static::$constants[$key])) {
            $this->fetchItems();
        }

        return static::$constants[$key];
    }

    /**
     * Get the structures.
     *
     * @return array
     */
    protected function getStructures()
    {
        $key = $this->getHashedFileName();

        if (! isset(static::$structures[$key])) {
            $this->fetchItems();
        }

        return static::$structures[$key];
    }

    /**
     * Fetch the items.
     *
     * @return void.
     */
    protected function fetchItems()
    {
        $key = $this->getHashedFileName();

        $classes = [];
        $functions = [];
        $constants = [];
        $structures = [];
        $tokens = $this->getFileTokens();

        $open = 0;
        $state = 'start';
        $lastState = '';
        $prefix = '';
        $name = '';
        $alias = '';
        $isFunc = $isConst = false;

        $startLine = $lastKnownLine = 0;
        $structType = $structName = '';
        $structIgnore = false;

        $namespace = '';
        $namespaceStartLine = 0;
        $namespaceBraced = false;
        $namespaceClasses = [];

        foreach ($tokens as $token) {
            if (is_array($token)) {
                $lastKnownLine = $token[2];
            }

            switch ($state) {
                case 'start':
                    switch ($token[0]) {
                        case T_NAMESPACE:
                            $structures[] = [
                                'type' => 'namespace',
                                'name' => $namespace,
                                'start' => $namespaceStartLine,
                                'end' => $token[2] - 1,
                                'classes' => $namespaceClasses,
                            ];
                            $namespace = '';
                            $namespaceClasses = [];
                            $state = 'namespace';
                            $namespaceStartLine = $token[2];
                            break;
                        case T_CLASS:
                        case T_INTERFACE:
                        case T_TRAIT:
                            $state = 'before_structure';
                            $startLine = $token[2];
                            $structType = $token[0] == T_CLASS
                                                    ? 'class'
                                                    : ($token[0] == T_INTERFACE ? 'interface' : 'trait');
                            break;
                        case T_USE:
                            $state = 'use';
                            $prefix = $name = $alias = '';
                            $isFunc = $isConst = false;
                            break;
                        case T_FUNCTION:
                            $state = 'structure';
                            $structIgnore = true;
                            break;
                        case T_NEW:
                            $state = 'new';
                            break;
                        case T_OBJECT_OPERATOR:
                        case T_DOUBLE_COLON:
                            $state = 'invoke';
                            break;
                        case '}':
                            if ($namespaceBraced) {
                                $structures[] = [
                                    'type' => 'namespace',
                                    'name' => $namespace,
                                    'start' => $namespaceStartLine,
                                    'end' => $lastKnownLine,
                                    'classes' => $namespaceClasses,
                                ];
                                $namespaceBraced = false;
                                $namespace = '';
                                $namespaceClasses = [];
                            }
                            break;
                    }
                    break;
                case 'namespace':
                    switch ($token[0]) {
                        case T_STRING:
                        case T_NAME_QUALIFIED:
                            $namespace = $token[1];
                            break;
                        case ';':
                        case '{':
                            $state = 'start';
                            $namespaceBraced = $token[0] === '{';
                            break;
                    }
                    break;
                case 'use':
                    switch ($token[0]) {
                        case T_FUNCTION:
                            $isFunc = true;
                            break;
                        case T_CONST:
                            $isConst = true;
                            break;
                        case T_NS_SEPARATOR:
                            $name .= $token[1];
                            break;
                        case T_STRING:
                            $name .= $token[1];
                            $alias = $token[1];
                            break;
                        case T_NAME_QUALIFIED:
                            $name .= $token[1];
                            $pieces = explode('\\', $token[1]);
                            $alias = end($pieces);
                            break;
                        case T_AS:
                            $lastState = 'use';
                            $state = 'alias';
                            break;
                        case '{':
                            $prefix = $name;
                            $name = $alias = '';
                            $state = 'use-group';
                            break;
                        case ',':
                        case ';':
                            if ($name === '' || $name[0] !== '\\') {
                                $name = '\\'.$name;
                            }

                            if ($alias !== '') {
                                if ($isFunc) {
                                    $functions[strtolower($alias)] = $name;
                                } elseif ($isConst) {
                                    $constants[$alias] = $name;
                                } else {
                                    $classes[strtolower($alias)] = $name;
                                    $namespaceClasses[strtolower($alias)] = $name;
                                }
                            }
                            $name = $alias = '';
                            $state = $token === ';' ? 'start' : 'use';
                            break;
                    }
                    break;
                case 'use-group':
                    switch ($token[0]) {
                        case T_NS_SEPARATOR:
                            $name .= $token[1];
                            break;
                        case T_NAME_QUALIFIED:
                            $name .= $token[1];
                            $pieces = explode('\\', $token[1]);
                            $alias = end($pieces);
                            break;
                        case T_STRING:
                            $name .= $token[1];
                            $alias = $token[1];
                            break;
                        case T_AS:
                            $lastState = 'use-group';
                            $state = 'alias';
                            break;
                        case ',':
                        case '}':

                            if ($prefix === '' || $prefix[0] !== '\\') {
                                $prefix = '\\'.$prefix;
                            }

                            if ($alias !== '') {
                                if ($isFunc) {
                                    $functions[strtolower($alias)] = $prefix.$name;
                                } elseif ($isConst) {
                                    $constants[$alias] = $prefix.$name;
                                } else {
                                    $classes[strtolower($alias)] = $prefix.$name;
                                    $namespaceClasses[strtolower($alias)] = $prefix.$name;
                                }
                            }
                            $name = $alias = '';
                            $state = $token === '}' ? 'use' : 'use-group';
                            break;
                    }
                    break;
                case 'alias':
                    if ($token[0] === T_STRING) {
                        $alias = $token[1];
                        $state = $lastState;
                    }
                    break;
                case 'new':
                    switch ($token[0]) {
                        case T_WHITESPACE:
                        case T_COMMENT:
                        case T_DOC_COMMENT:
                            break 2;
                        case T_CLASS:
                            $state = 'structure';
                            $structIgnore = true;
                            break;
                        default:
                            $state = 'start';
                    }
                    break;
                case 'invoke':
                    switch ($token[0]) {
                        case T_WHITESPACE:
                        case T_COMMENT:
                        case T_DOC_COMMENT:
                            break 2;
                        default:
                            $state = 'start';
                    }
                    break;
                case 'before_structure':
                    if ($token[0] == T_STRING) {
                        $structName = $token[1];
                        $state = 'structure';
                    }
                    break;
                case 'structure':
                    switch ($token[0]) {
                        case '{':
                        case T_CURLY_OPEN:
                        case T_DOLLAR_OPEN_CURLY_BRACES:
                            $open++;
                            break;
                        case '}':
                            if (--$open == 0) {
                                if (! $structIgnore) {
                                    $structures[] = [
                                        'type' => $structType,
                                        'name' => $structName,
                                        'start' => $startLine,
                                        'end' => $lastKnownLine,
                                    ];
                                }
                                $structIgnore = false;
                                $state = 'start';
                            }
                            break;
                    }
                    break;
            }
        }

        $structures[] = [
            'type' => 'namespace',
            'name' => $namespace,
            'start' => $namespaceStartLine,
            'end' => PHP_INT_MAX,
            'classes' => $namespaceClasses,
        ];

        static::$classes[$key] = $classes;
        static::$functions[$key] = $functions;
        static::$constants[$key] = $constants;
        static::$structures[$key] = $structures;
    }

    /**
     * Returns the namespace associated to the closure.
     *
     * @return string
     */
    protected function getClosureNamespaceName()
    {
        $startLine = $this->getStartLine();
        $endLine = $this->getEndLine();

        foreach ($this->getStructures() as $struct) {
            if ($struct['type'] === 'namespace' &&
                $struct['start'] <= $startLine &&
                $struct['end'] >= $endLine
            ) {
                return $struct['name'];
            }
        }

        return '';
    }

    /**
     * Parse the given token.
     *
     * @param  string  $token
     * @return array
     */
    protected function parseNameQualified($token)
    {
        $pieces = explode('\\', $token);

        $id_start = array_shift($pieces);

        $id_start_ci = strtolower($id_start);

        $id_name = '\\'.implode('\\', $pieces);

        return [$id_start, $id_start_ci, $id_name];
    }

    /**
     * Collect a closure candidate and reset state for finding the next one.
     *
     * @param  array  $candidates
     * @param  string  $code
     * @param  array  $use
     * @param  bool  $isShortClosure
     * @param  bool  $isUsingThisObject
     * @param  bool  $isUsingScope
     * @return array
     */
    protected function collectCandidate(&$candidates, $code, $use, $isShortClosure, $isUsingThisObject, $isUsingScope)
    {
        $candidates[] = [
            'code' => $code,
            'use' => $use,
            'isShortClosure' => $isShortClosure,
            'isUsingThisObject' => $isUsingThisObject,
            'isUsingScope' => $isUsingScope,
        ];

        return [
            'code' => '',
            'state' => 'start',
            'open' => 0,
            'use' => [],
            'isShortClosure' => false,
            'isUsingThisObject' => false,
            'isUsingScope' => false,
            'closureArgsInnerFuncCount' => 0,
            'closureArgsBraceDepth' => 0,
        ];
    }

    /**
     * Apply a candidate's properties to this instance.
     *
     * @param  array  $candidate
     * @return void
     */
    protected function applyCandidate($candidate)
    {
        if ($candidate['isShortClosure']) {
            $this->useVariables = $this->getStaticVariables();
        } else {
            $this->useVariables = empty($candidate['use'])
                ? $candidate['use']
                : array_intersect_key($this->getStaticVariables(), array_flip($candidate['use']));
        }

        $this->isShortClosure = $candidate['isShortClosure'];
        $this->isBindingRequired = $candidate['isUsingThisObject'];
        $this->isScopeRequired = $candidate['isUsingScope'];
    }

    /**
     * Verify that a candidate matches the closure's signature.
     *
     * @param  array  $candidate
     * @return bool
     */
    protected function verifyCandidateSignature($candidate)
    {
        $code = $candidate['code'];
        $use = $candidate['use'];
        $isShortClosure = $candidate['isShortClosure'];

        // Check if code starts with 'static' (more precise than searching anywhere in code)
        $isStaticCode = strtolower(substr(ltrim($code), 0, 6)) === 'static';
        if (parent::isStatic() !== $isStaticCode) {
            return false;
        }

        // Parse the candidate to extract parameters and variables
        $tokens = token_get_all('<?php '.$code);
        $params = [];
        $vars = [];
        $state = 'start';

        foreach ($tokens as $token) {
            if (! is_array($token)) {
                if ($token === '(' && $state === 'start') {
                    $state = 'params';
                } elseif ($token === ')' && $state === 'params') {
                    $state = 'body';
                }

                continue;
            }

            if ($token[0] === T_VARIABLE) {
                $name = substr($token[1], 1);

                if ($state === 'params') {
                    $params[] = $name;
                } elseif ($state === 'body' && $name !== 'this') {
                    $vars[$name] = true;
                }
            }
        }

        // Verify parameter count
        if (parent::getNumberOfParameters() !== count($params)) {
            return false;
        }

        // Verify use/captured variables
        if ($isShortClosure) {
            $actualVars = array_keys(parent::getStaticVariables());
            $foundCaptures = array_diff(array_keys($vars), $params);

            if (count($foundCaptures) !== count($actualVars)) {
                return false;
            }

            if (count(array_diff($foundCaptures, $actualVars)) > 0) {
                return false;
            }
        } else {
            $actualStaticVariables = array_keys(parent::getStaticVariables());

            if (! empty($use) && count(array_diff($use, $actualStaticVariables)) > 0) {
                return false;
            }

            if (count($use) !== count(parent::getStaticVariables())) {
                return false;
            }
        }

        return true;
    }
}
