Code generation
===============

It is also possible to generate code using the parser, by first creating an Abstract Syntax Tree and then using the
pretty printer to convert it to PHP code. To simplify code generation, the project comes with a set of builders for
common structures as well as simple templating support. Both features are described in the following:

Builders
--------

The project provides builders for classes, interfaces, methods, functions, parameters and properties, which
allow creating node trees with a fluid interface, instead of instantiating all nodes manually.

Here is an example:

```php
<?php
$factory = new PHPParser_BuilderFactory;
$node = $factory->class('SomeClass')
    ->extend('SomeOtherClass')
    ->implement('A\Few', 'Interfaces')
    ->makeAbstract() // ->makeFinal()

    ->addStmt($factory->method('someMethod')
        ->makeAbstract() // ->makeFinal()
        ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
    )

    ->addStmt($factory->method('anotherMethod')
        ->makeProtected() // ->makePublic() [default], ->makePrivate()
        ->addParam($factory->param('someParam')->setDefault('test'))
        // it is possible to add manually created nodes
        ->addStmt(new PHPParser_Node_Expr_Print(new PHPParser_Node_Expr_Variable('someParam')))
    )

    // properties will be correctly reordered above the methods
    ->addStmt($factory->property('someProperty')->makeProtected())
    ->addStmt($factory->property('anotherProperty')->makePrivate()->setDefault(array(1, 2, 3)))

    ->getNode()
;

$stmts = array($node);
echo $prettyPrinter->prettyPrint($stmts);
```

This will produce the following output with the default pretty printer:

```php
<?php
abstract class SomeClass extends SomeOtherClass implements A\Few, Interfaces
{
    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);
    abstract function someMethod(SomeClass $someParam);
    protected function anotherMethod($someParam = 'test')
    {
        print $someParam;
    }
}
```

Templates
---------

> **DEPRECATED**: This feature is deprecated and will be removed in PHP-Parser 1.0.

Additionally it is possible to generate code from reusable templates.

As an example consider the following template, which defines a general getter/setter skeleton in terms of a property
`__name__` and its `__type__`:

```php
<?php

class GetterSetterTemplate
{
    /**
     * @var __type__ The __name__
     */
    protected $__name__;

    /**
     * Gets the __name__.
     *
     * @return __type__ The __name__
     */
    public function get__Name__() {
        return $this->__name__;
    }

    /**
     * Sets the __name__.
     *
     * @param __type__ $__name__ The new __name__
     */
    public function set__Name__($__name__) {
        $this->__name__ = $__name__;
    }
}
```

Using this template we can easily create a class with multiple properties and their respective getters and setters:

```php
<?php

// $templateString contains the above template
$template = new PHPParser_Template($parser, $templateString);

// We only have to specify the __name__ placeholder, as the
// capitalized __Name__ placeholder is automatically created
$properties = [
    ['name' => 'title',     'type' => 'string'],
    ['name' => 'body',      'type' => 'string'],
    ['name' => 'author',    'type' => 'User'],
    ['name' => 'timestamp', 'type' => 'DateTime'],
];

$class = $factory->class('BlogPost')->implement('Post');

foreach ($properties as $propertyPlaceholders) {
    $stmts = $template->getStmts($propertyPlaceholders);

    $class->addStmts(
        // $stmts contains all statements from the template. So [0] fetches the class statement
        // and ->stmts retrieves the methods.
        $stmts[0]->stmts
    );
}

echo $prettyPrinter->prettyPrint(array($class->getNode()));
```

The result would look roughly like this:

```php
<?php

class BlogPost implements Post
{
    /**
     * @var string The title
     */
    protected $title;

    /**
     * @var string The body
     */
    protected $body;

    /**
     * @var User The author
     */
    protected $author;

    /**
     * @var DateTime The timestamp
     */
    protected $timestamp;

    /**
     * Gets the title.
     *
     * @return string The title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title The new title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the body.
     *
     * @return string The body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the body.
     *
     * @param string $body The new body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Gets the author.
     *
     * @return User The author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the author.
     *
     * @param User $author The new author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Gets the timestamp.
     *
     * @return DateTime The timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets the timestamp.
     *
     * @param DateTime $timestamp The new timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
}
```

When using multiple templates it is easier to manage them on the filesystem. They can be loaded using the
`TemplateLoader`:

```php
<?php

// We'll store our templates in ./templates and give them a .php suffix
$loader = new PHPParser_TemplateLoader($parser, './templates', '.php');

// loads ./templates/GetterSetter.php
$getterSetterTemplate = $loader->load('GetterSetter');

// loads ./templates/Collection.php
$collectionTemplate = $loader->load('Collection');

// The use of a suffix is optional. The following code for example is equivalent:
$loader = new PHPParser_TemplateLoader($parser, './templates');

// loads ./templates/GetterSetter.php
$getterSetterTemplate = $loader->load('GetterSetter.php');

// loads ./templates/Collection.php
$collectionTemplate = $loader->load('Collection.php');
```