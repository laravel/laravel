<?php

class PHPParser_Tests_NodeVisitor_NameResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers PHPParser_NodeVisitor_NameResolver
     */
    public function testResolveNames() {
        $code = <<<EOC
<?php

namespace Foo {
    use Hallo as Hi;

    new Bar();
    new Hi();
    new Hi\\Bar();
    new \\Bar();
    new namespace\\Bar();

    bar();
    hi();
    Hi\\bar();
    foo\\bar();
    \\bar();
    namespace\\bar();
}
namespace {
    use Hallo as Hi;

    new Bar();
    new Hi();
    new Hi\\Bar();
    new \\Bar();
    new namespace\\Bar();

    bar();
    hi();
    Hi\\bar();
    foo\\bar();
    \\bar();
    namespace\\bar();
}
EOC;
        $expectedCode = <<<EOC
namespace Foo {
    use Hallo as Hi;
    new \\Foo\\Bar();
    new \\Hallo();
    new \\Hallo\\Bar();
    new \\Bar();
    new \\Foo\\Bar();
    bar();
    hi();
    \\Hallo\\bar();
    \\Foo\\foo\\bar();
    \\bar();
    \\Foo\\bar();
}
namespace {
    use Hallo as Hi;
    new \\Bar();
    new \\Hallo();
    new \\Hallo\\Bar();
    new \\Bar();
    new \\Bar();
    bar();
    hi();
    \\Hallo\\bar();
    \\foo\\bar();
    \\bar();
    \\bar();
}
EOC;

        $parser        = new PHPParser_Parser(new PHPParser_Lexer_Emulative);
        $prettyPrinter = new PHPParser_PrettyPrinter_Default;
        $traverser     = new PHPParser_NodeTraverser;
        $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);

        $stmts = $parser->parse($code);
        $stmts = $traverser->traverse($stmts);

        $this->assertEquals($expectedCode, $prettyPrinter->prettyPrint($stmts));
    }

    /**
     * @covers PHPParser_NodeVisitor_NameResolver
     */
    public function testResolveLocations() {
        $code = <<<EOC
<?php
namespace NS;

class A extends B implements C {
    use A;
}

interface A extends C {
    public function a(A \$a);
}

A::b();
A::\$b;
A::B;
new A;
\$a instanceof A;

namespace\a();
namespace\A;

try {
    \$someThing;
} catch (A \$a) {
    \$someThingElse;
}
EOC;
        $expectedCode = <<<EOC
namespace NS;

class A extends \\NS\\B implements \\NS\\C
{
    use \\NS\\A;
}
interface A extends \\NS\\C
{
    public function a(\\NS\\A \$a);
}
\\NS\\A::b();
\\NS\\A::\$b;
\\NS\\A::B;
new \\NS\\A();
\$a instanceof \\NS\\A;
\\NS\\a();
\\NS\\A;
try {
    \$someThing;
} catch (\\NS\\A \$a) {
    \$someThingElse;
}
EOC;

        $parser        = new PHPParser_Parser(new PHPParser_Lexer_Emulative);
        $prettyPrinter = new PHPParser_PrettyPrinter_Default;
        $traverser     = new PHPParser_NodeTraverser;
        $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);

        $stmts = $parser->parse($code);
        $stmts = $traverser->traverse($stmts);

        $this->assertEquals($expectedCode, $prettyPrinter->prettyPrint($stmts));
    }

    public function testNoResolveSpecialName() {
        $stmts = array(new PHPParser_Node_Expr_New(new PHPParser_Node_Name('self')));

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    protected function createNamespacedAndNonNamespaced(array $stmts) {
        return array(
            new PHPParser_Node_Stmt_Namespace(new PHPParser_Node_Name('NS'), $stmts),
            new PHPParser_Node_Stmt_Namespace(null,                          $stmts),
        );
    }

    public function testAddNamespacedName() {
        $stmts = $this->createNamespacedAndNonNamespaced(array(
            new PHPParser_Node_Stmt_Class('A'),
            new PHPParser_Node_Stmt_Interface('B'),
            new PHPParser_Node_Stmt_Function('C'),
            new PHPParser_Node_Stmt_Const(array(
                new PHPParser_Node_Const('D', new PHPParser_Node_Scalar_String('E'))
            )),
        ));

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);

        $stmts = $traverser->traverse($stmts);

        $this->assertEquals('NS\\A', (string) $stmts[0]->stmts[0]->namespacedName);
        $this->assertEquals('NS\\B', (string) $stmts[0]->stmts[1]->namespacedName);
        $this->assertEquals('NS\\C', (string) $stmts[0]->stmts[2]->namespacedName);
        $this->assertEquals('NS\\D', (string) $stmts[0]->stmts[3]->consts[0]->namespacedName);
        $this->assertEquals('A',     (string) $stmts[1]->stmts[0]->namespacedName);
        $this->assertEquals('B',     (string) $stmts[1]->stmts[1]->namespacedName);
        $this->assertEquals('C',     (string) $stmts[1]->stmts[2]->namespacedName);
        $this->assertEquals('D',     (string) $stmts[1]->stmts[3]->consts[0]->namespacedName);
    }

    public function testAddTraitNamespacedName() {
        $stmts = $this->createNamespacedAndNonNamespaced(array(
            new PHPParser_Node_Stmt_Trait('A')
        ));

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);

        $stmts = $traverser->traverse($stmts);

        $this->assertEquals('NS\\A', (string) $stmts[0]->stmts[0]->namespacedName);
        $this->assertEquals('A',     (string) $stmts[1]->stmts[0]->namespacedName);
    }

    /**
     * @expectedException        PHPParser_Error
     * @expectedExceptionMessage Cannot use "C" as "B" because the name is already in use on line 2
     */
    public function testAlreadyInUseError() {
        $stmts = array(
            new PHPParser_Node_Stmt_Use(array(
                new PHPParser_Node_Stmt_UseUse(new PHPParser_Node_Name('A\B'), 'B', array('startLine' => 1)),
                new PHPParser_Node_Stmt_UseUse(new PHPParser_Node_Name('C'),   'B', array('startLine' => 2)),
            ))
        );

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);
        $traverser->traverse($stmts);
    }

    public function testClassNameIsCaseInsensitive()
    {
        $source = <<<EOC
<?php
namespace Foo;
use Bar\\Baz;
\$test = new baz();
EOC;

        $parser = new PHPParser_Parser(new PHPParser_Lexer_Emulative);
        $stmts = $parser->parse($source);

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);

        $stmts = $traverser->traverse($stmts);
        $stmt = $stmts[0];

        $this->assertEquals(array('Bar', 'Baz'), $stmt->stmts[1]->expr->class->parts);
    }
}
