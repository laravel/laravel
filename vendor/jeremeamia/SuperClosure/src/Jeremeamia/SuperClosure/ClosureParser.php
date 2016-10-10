<?php

namespace Jeremeamia\SuperClosure;

use Jeremeamia\SuperClosure\Visitor\ClosureFinderVisitor;
use Jeremeamia\SuperClosure\Visitor\MagicConstantVisitor;

/**
 * Parses a closure from its reflection such that the code and used (closed upon) variables are accessible. The
 * ClosureParser uses the fabulous nikic/php-parser library which creates abstract syntax trees (AST) of the code.
 *
 * @copyright Jeremy Lindblom 2010-2013
 */
class ClosureParser
{
    /**
     * @var array
     */
    protected static $cache = array();

    /**
     * @var \ReflectionFunction The reflection of the closure being parsed
     */
    protected $reflection;

    /**
     * @var \PHPParser_Node An abstract syntax tree defining the code of the closure
     */
    protected $abstractSyntaxTree;

    /**
     * @var array The variables used (closed upon) by the closure and their values
     */
    protected $usedVariables;

    /**
     * @var  string The closure's code
     */
    protected $code;

    /**
     * Creates a ClosureParser for the provided closure
     *
     * @param \Closure $closure
     *
     * @return ClosureParser
     */
    public static function fromClosure(\Closure $closure)
    {
        return new self(new \ReflectionFunction($closure));
    }

    /**
     * Clears the internal cache of file ASTs.
     *
     * ASTs are stored for any file that is parsed to speed up multiple
     * parsings of the same file. If you are worried about the memory consumption of files the ClosureParser has already
     * parsed, you can call this function to clear the cache. The cache is not persistent and stores ASTs from the
     * current process
     */
    public static function clearCache()
    {
        self::$cache = array();
    }

    /**
     * @param \ReflectionFunction $reflection
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(\ReflectionFunction $reflection)
    {
        if (!$reflection->isClosure()) {
            throw new \InvalidArgumentException('You must provide the reflection of a closure.');
        }

        $this->reflection = $reflection;
    }

    /**
     * Returns the reflection of the closure
     *
     * @return \ReflectionFunction
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * Returns the abstract syntax tree (AST) of the closure's code. Class names are resolved to their fully-qualified
     * class names (FQCN) and magic constants are resolved to their values as they would be in the context of the
     * closure.
     *
     * @return \PHPParser_Node_Expr_Closure
     * @throws \InvalidArgumentException
     */
    public function getClosureAbstractSyntaxTree()
    {
        if (!$this->abstractSyntaxTree) {
            try {
                // Parse the code from the file containing the closure and create an AST with FQCN resolved
                $fileAst = $this->getFileAbstractSyntaxTree();
                $closureFinder = new ClosureFinderVisitor($this->reflection);
                $fileTraverser = new \PHPParser_NodeTraverser();
                $fileTraverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver);
                $fileTraverser->addVisitor($closureFinder);
                $fileTraverser->traverse($fileAst);
            } catch (\PHPParser_Error $e) {
                // @codeCoverageIgnoreStart
                throw new \InvalidArgumentException('There was an error parsing the file containing the closure.');
                // @codeCoverageIgnoreEnd
            }

            // Find the first closure defined in the AST that is on the line where the closure is located
            $closureAst = $closureFinder->getClosureNode();
            if (!$closureAst) {
                // @codeCoverageIgnoreStart
                throw new \InvalidArgumentException('The closure was not found within the abstract syntax tree.');
                // @codeCoverageIgnoreEnd
            }

            // Resolve additional nodes by making a second pass through just the closure's nodes
            $closureTraverser = new \PHPParser_NodeTraverser();
            $closureTraverser->addVisitor(new MagicConstantVisitor($closureFinder->getLocation()));
            $closureAst = $closureTraverser->traverse(array($closureAst));
            $this->abstractSyntaxTree = $closureAst[0];
        }

        return $this->abstractSyntaxTree;
    }

    /**
     * Returns the variables that in the "use" clause of the closure definition. These are referred to as the "used
     * variables", "static variables", or "closed upon variables", "context" of the closure.
     *
     * @return array
     */
    public function getUsedVariables()
    {
        if (!$this->usedVariables) {
            // Get the variable names defined in the AST
            $usedVarNames = array_map(function ($usedVar) {
                return $usedVar->var;
            }, $this->getClosureAbstractSyntaxTree()->uses);

            // Get the variable names and values using reflection
            $usedVarValues = $this->reflection->getStaticVariables();

            // Combine the two arrays to create a canonical hash of variable names and values
            $this->usedVariables = array();
            foreach ($usedVarNames as $name) {
                if (array_key_exists($name, $usedVarValues)) {
                    $this->usedVariables[$name] = $usedVarValues[$name];
                }
            }
        }

        return $this->usedVariables;
    }

    /**
     * Returns the formatted code of the closure
     *
     * @return string
     */
    public function getCode()
    {
        if (!$this->code) {
            // Use the pretty printer to print the closure code from the AST
            $printer = new \PHPParser_PrettyPrinter_Default();
            $this->code = $printer->prettyPrint(array($this->getClosureAbstractSyntaxTree()));
        }

        return $this->code;
    }

    /**
     * Loads the PHP file and produces an abstract syntax tree (AST) of the code. This is stored in an internal cache by
     * the filename for memoization within the same process
     *
     * @return array
     */
    protected function getFileAbstractSyntaxTree()
    {
        $filename = $this->reflection->getFileName();

        if (!isset(self::$cache[$filename])) {
            $parser = new \PHPParser_Parser(new \PHPParser_Lexer_Emulative);
            self::$cache[$filename] = $parser->parse(file_get_contents($filename));
        }

        return self::$cache[$filename];
    }
}
