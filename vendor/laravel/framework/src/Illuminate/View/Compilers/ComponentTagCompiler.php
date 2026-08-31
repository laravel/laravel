<?php

namespace Illuminate\View\Compilers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\AnonymousComponent;
use Illuminate\View\DynamicComponent;
use Illuminate\View\ViewFinderInterface;
use InvalidArgumentException;
use ReflectionClass;

/**
 * @author Spatie bvba <info@spatie.be>
 * @author Taylor Otwell <taylor@laravel.com>
 */
class ComponentTagCompiler
{
    /**
     * The Blade compiler instance.
     *
     * @var \Illuminate\View\Compilers\BladeCompiler
     */
    protected $blade;

    /**
     * The component class aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * The component class namespaces.
     *
     * @var array
     */
    protected $namespaces = [];

    /**
     * The "bind:" attributes that have been compiled for the current component.
     *
     * @var array
     */
    protected $boundAttributes = [];

    /**
     * Create a new component tag compiler.
     *
     * @param  array  $aliases
     * @param  array  $namespaces
     * @param  \Illuminate\View\Compilers\BladeCompiler|null  $blade
     */
    public function __construct(array $aliases = [], array $namespaces = [], ?BladeCompiler $blade = null)
    {
        $this->aliases = $aliases;
        $this->namespaces = $namespaces;

        $this->blade = $blade ?: new BladeCompiler(new Filesystem, sys_get_temp_dir());
    }

    /**
     * Compile the component and slot tags within the given string.
     *
     * @param  string  $value
     * @return string
     */
    public function compile(string $value)
    {
        $value = $this->compileSlots($value);

        return $this->compileTags($value);
    }

    /**
     * Compile the tags within the given string.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function compileTags(string $value)
    {
        $value = $this->compileSelfClosingTags($value);
        $value = $this->compileOpeningTags($value);
        $value = $this->compileClosingTags($value);

        return $value;
    }

    /**
     * Compile the opening tags within the given string.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function compileOpeningTags(string $value)
    {
        $pattern = "/
            <
                \s*
                x[-\:]([\w\-\:\.]*)
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                (\:\\\$)(\w+)
                            )
                            |
                            (?:
                                [\w\-:.@%]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
                (?<![\/=\-])
            >
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            return $this->componentString($matches[1], $attributes);
        }, $value);
    }

    /**
     * Compile the self-closing tags within the given string.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function compileSelfClosingTags(string $value)
    {
        $pattern = "/
            <
                \s*
                x[-\:]([\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                (\:\\\$)(\w+)
                            )
                            |
                            (?:
                                [\w\-:.@%]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
            \/>
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            return $this->componentString($matches[1], $attributes)."\n@endComponentClass##END-COMPONENT-CLASS##";
        }, $value);
    }

    /**
     * Compile the Blade component string for the given component and attributes.
     *
     * @param  string  $component
     * @param  array  $attributes
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function componentString(string $component, array $attributes)
    {
        $class = $this->componentClass($component);

        [$data, $attributes] = $this->partitionDataAndAttributes($class, $attributes);

        $data = $data->mapWithKeys(function ($value, $key) {
            return [Str::camel($key) => $value];
        });

        // If the component doesn't exist as a class, we'll assume it's a class-less
        // component and pass the component as a view parameter to the data so it
        // can be accessed within the component and we can render out the view.
        if (! class_exists($class)) {
            $view = Str::startsWith($component, 'mail::')
                ? "\$__env->getContainer()->make(Illuminate\\View\\Factory::class)->make('{$component}')"
                : "'$class'";

            $parameters = [
                'view' => $view,
                'data' => '['.$this->attributesToString($data->all(), $escapeBound = false).']',
            ];

            $class = AnonymousComponent::class;
        } else {
            $parameters = $data->all();
        }

        return "##BEGIN-COMPONENT-CLASS##@component('{$class}', '{$component}', [".$this->attributesToString($parameters, $escapeBound = false).'])
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\\'.$class.'::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['.$this->attributesToString($attributes->all(), $escapeAttributes = $class !== DynamicComponent::class).']); ?>';
    }

    /**
     * Get the component class for a given component alias.
     *
     * @param  string  $component
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function componentClass(string $component)
    {
        $viewFactory = Container::getInstance()->make(Factory::class);

        if (isset($this->aliases[$component])) {
            if (class_exists($alias = $this->aliases[$component])) {
                return $alias;
            }

            if ($viewFactory->exists($alias)) {
                return $alias;
            }

            throw new InvalidArgumentException(
                "Unable to locate class or view [{$alias}] for component [{$component}]."
            );
        }

        if ($class = $this->findClassByComponent($component)) {
            return $class;
        }

        if (class_exists($class = $this->guessClassName($component))) {
            return $class;
        }

        if (class_exists($class = $class.'\\'.Str::afterLast($class, '\\'))) {
            return $class;
        }

        if (! is_null($guess = $this->guessAnonymousComponentUsingNamespaces($viewFactory, $component)) ||
            ! is_null($guess = $this->guessAnonymousComponentUsingPaths($viewFactory, $component))) {
            return $guess;
        }

        if (Str::startsWith($component, 'mail::')) {
            return $component;
        }

        throw new InvalidArgumentException(
            "Unable to locate a class or view for component [{$component}]."
        );
    }

    /**
     * Attempt to find an anonymous component using the registered anonymous component paths.
     *
     * @param  \Illuminate\Contracts\View\Factory  $viewFactory
     * @param  string  $component
     * @return string|null
     */
    protected function guessAnonymousComponentUsingPaths(Factory $viewFactory, string $component)
    {
        $delimiter = ViewFinderInterface::HINT_PATH_DELIMITER;

        foreach ($this->blade->getAnonymousComponentPaths() as $path) {
            try {
                if (str_contains($component, $delimiter) &&
                    ! str_starts_with($component, $path['prefix'].$delimiter)) {
                    continue;
                }

                $formattedComponent = str_starts_with($component, $path['prefix'].$delimiter)
                    ? Str::after($component, $delimiter)
                    : $component;

                if (! is_null($guess = match (true) {
                    $viewFactory->exists($guess = $path['prefixHash'].$delimiter.$formattedComponent) => $guess,
                    $viewFactory->exists($guess = $path['prefixHash'].$delimiter.$formattedComponent.'.index') => $guess,
                    $viewFactory->exists($guess = $path['prefixHash'].$delimiter.$formattedComponent.'.'.Str::afterLast($formattedComponent, '.')) => $guess,
                    default => null,
                })) {
                    return $guess;
                }
            } catch (InvalidArgumentException) {
                //
            }
        }
    }

    /**
     * Attempt to find an anonymous component using the registered anonymous component namespaces.
     *
     * @param  \Illuminate\Contracts\View\Factory  $viewFactory
     * @param  string  $component
     * @return string|null
     */
    protected function guessAnonymousComponentUsingNamespaces(Factory $viewFactory, string $component)
    {
        return (new Collection($this->blade->getAnonymousComponentNamespaces()))
            ->filter(function ($directory, $prefix) use ($component) {
                return Str::startsWith($component, $prefix.'::');
            })
            ->prepend('components', $component)
            ->reduce(function ($carry, $directory, $prefix) use ($component, $viewFactory) {
                if (! is_null($carry)) {
                    return $carry;
                }

                $componentName = Str::after($component, $prefix.'::');

                if ($viewFactory->exists($view = $this->guessViewName($componentName, $directory))) {
                    return $view;
                }

                if ($viewFactory->exists($view = $this->guessViewName($componentName, $directory).'.index')) {
                    return $view;
                }

                $lastViewSegment = Str::afterLast(Str::afterLast($componentName, '.'), ':');

                if ($viewFactory->exists($view = $this->guessViewName($componentName, $directory).'.'.$lastViewSegment)) {
                    return $view;
                }
            });
    }

    /**
     * Find the class for the given component using the registered namespaces.
     *
     * @param  string  $component
     * @return string|null
     */
    public function findClassByComponent(string $component)
    {
        $segments = explode('::', $component);

        $prefix = $segments[0];

        if (! isset($this->namespaces[$prefix], $segments[1])) {
            return;
        }

        if (class_exists($class = $this->namespaces[$prefix].'\\'.$this->formatClassName($segments[1]))) {
            return $class;
        }

        if (class_exists($class = $class.'\\'.Str::afterLast($class, '\\'))) {
            return $class;
        }
    }

    /**
     * Guess the class name for the given component.
     *
     * @param  string  $component
     * @return string
     */
    public function guessClassName(string $component)
    {
        $namespace = Container::getInstance()
            ->make(Application::class)
            ->getNamespace();

        $class = $this->formatClassName($component);

        return $namespace.'View\\Components\\'.$class;
    }

    /**
     * Format the class name for the given component.
     *
     * @param  string  $component
     * @return string
     */
    public function formatClassName(string $component)
    {
        $componentPieces = array_map(function ($componentPiece) {
            return ucfirst(Str::camel($componentPiece));
        }, explode('.', $component));

        return implode('\\', $componentPieces);
    }

    /**
     * Guess the view name for the given component.
     *
     * @param  string  $name
     * @param  string  $prefix
     * @return string
     */
    public function guessViewName($name, $prefix = 'components.')
    {
        if (! Str::endsWith($prefix, '.')) {
            $prefix .= '.';
        }

        $delimiter = ViewFinderInterface::HINT_PATH_DELIMITER;

        if (str_contains($name, $delimiter)) {
            return Str::replaceFirst($delimiter, $delimiter.$prefix, $name);
        }

        return $prefix.$name;
    }

    /**
     * Partition the data and extra attributes from the given array of attributes.
     *
     * @param  string  $class
     * @param  array  $attributes
     * @return array
     */
    public function partitionDataAndAttributes($class, array $attributes)
    {
        // If the class doesn't exist, we'll assume it is a class-less component and
        // return all of the attributes as both data and attributes since we have
        // now way to partition them. The user can exclude attributes manually.
        if (! class_exists($class)) {
            return [new Collection($attributes), new Collection($attributes)];
        }

        $constructor = (new ReflectionClass($class))->getConstructor();

        $parameterNames = $constructor
            ? (new Collection($constructor->getParameters()))->map->getName()->all()
            : [];

        return (new Collection($attributes))
            ->partition(fn ($value, $key) => in_array(Str::camel($key), $parameterNames))
            ->all();
    }

    /**
     * Compile the closing tags within the given string.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileClosingTags(string $value)
    {
        return preg_replace("/<\/\s*x[-\:][\w\-\:\.]*\s*>/", ' @endComponentClass##END-COMPONENT-CLASS##', $value);
    }

    /**
     * Compile the slot tags within the given string.
     *
     * @param  string  $value
     * @return string
     */
    public function compileSlots(string $value)
    {
        $pattern = "/
            <
                \s*
                x[\-\:]slot
                (?:\:(?<inlineName>\w+(?:-\w+)*))?
                (?:\s+name=(?<name>(\"[^\"]+\"|\\\'[^\\\']+\\\'|[^\s>]+)))?
                (?:\s+\:name=(?<boundName>(\"[^\"]+\"|\\\'[^\\\']+\\\'|[^\s>]+)))?
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
                (?<![\/=\-])
            >
        /x";

        $value = preg_replace_callback($pattern, function ($matches) {
            $name = $this->stripQuotes($matches['inlineName'] ?: $matches['name'] ?: $matches['boundName']) ?: "'slot'";

            if (Str::contains($name, '-') && ! empty($matches['inlineName'])) {
                $name = Str::camel($name);
            }

            // If the name was given as a simple string, we will wrap it in quotes as if it was bound for convenience...
            if (! empty($matches['inlineName']) || ! empty($matches['name'])) {
                $name = "'{$name}'";
            }

            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            // If an inline name was provided and a name or bound name was *also* provided, we will assume the name should be an attribute...
            if (! empty($matches['inlineName']) && (! empty($matches['name']) || ! empty($matches['boundName']))) {
                $attributes = ! empty($matches['name'])
                    ? array_merge($attributes, $this->getAttributesFromAttributeString('name='.$matches['name']))
                    : array_merge($attributes, $this->getAttributesFromAttributeString(':name='.$matches['boundName']));
            }

            return " @slot({$name}, null, [".$this->attributesToString($attributes).']) ';
        }, $value);

        return preg_replace('/<\/\s*x[\-\:]slot[^>]*>/', ' @endslot', $value);
    }

    /**
     * Get an array of attributes from the given attribute string.
     *
     * @param  string  $attributeString
     * @return array
     */
    protected function getAttributesFromAttributeString(string $attributeString)
    {
        $attributeString = $this->parseShortAttributeSyntax($attributeString);
        $attributeString = $this->parseAttributeBag($attributeString);
        $attributeString = $this->parseComponentTagClassStatements($attributeString);
        $attributeString = $this->parseComponentTagStyleStatements($attributeString);
        $attributeString = $this->parseBindAttributes($attributeString);

        $pattern = '/
            (?<attribute>[\w\-:.@%]+)
            (
                =
                (?<value>
                    (
                        \"[^\"]+\"
                        |
                        \\\'[^\\\']+\\\'
                        |
                        [^\s>]+
                    )
                )
            )?
        /x';

        if (! preg_match_all($pattern, $attributeString, $matches, PREG_SET_ORDER)) {
            return [];
        }

        return (new Collection($matches))->mapWithKeys(function ($match) {
            $attribute = $match['attribute'];
            $value = $match['value'] ?? null;

            if (is_null($value)) {
                $value = 'true';

                $attribute = Str::start($attribute, 'bind:');
            }

            $value = $this->stripQuotes($value);

            if (str_starts_with($attribute, 'bind:')) {
                $attribute = Str::after($attribute, 'bind:');

                $this->boundAttributes[$attribute] = true;
            } else {
                $value = "'".$this->compileAttributeEchos($value)."'";
            }

            if (str_starts_with($attribute, '::')) {
                $attribute = substr($attribute, 1);
            }

            return [$attribute => $value];
        })->toArray();
    }

    /**
     * Parses a short attribute syntax like :$foo into a fully-qualified syntax like :foo="$foo".
     *
     * @param  string  $value
     * @return string
     */
    protected function parseShortAttributeSyntax(string $value)
    {
        $pattern = "/\s\:\\\$(\w+)/x";

        return preg_replace_callback($pattern, function (array $matches) {
            return " :{$matches[1]}=\"\${$matches[1]}\"";
        }, $value);
    }

    /**
     * Parse the attribute bag in a given attribute string into its fully-qualified syntax.
     *
     * @param  string  $attributeString
     * @return string
     */
    protected function parseAttributeBag(string $attributeString)
    {
        $pattern = "/
            (?:^|\s+)                                        # start of the string or whitespace between attributes
            \{\{\s*(\\\$attributes(?:[^}]+?(?<!\s))?)\s*\}\} # exact match of attributes variable being echoed
        /x";

        return preg_replace($pattern, ' :attributes="$1"', $attributeString);
    }

    /**
     * Parse @class statements in a given attribute string into their fully-qualified syntax.
     *
     * @param  string  $attributeString
     * @return string
     */
    protected function parseComponentTagClassStatements(string $attributeString)
    {
        return preg_replace_callback(
            '/@(class)(\( ( (?>[^()]+) | (?2) )* \))/x', function ($match) {
                if ($match[1] === 'class') {
                    $match[2] = str_replace('"', "'", $match[2]);

                    return ":class=\"\Illuminate\Support\Arr::toCssClasses{$match[2]}\"";
                }

                return $match[0];
            }, $attributeString
        );
    }

    /**
     * Parse @style statements in a given attribute string into their fully-qualified syntax.
     *
     * @param  string  $attributeString
     * @return string
     */
    protected function parseComponentTagStyleStatements(string $attributeString)
    {
        return preg_replace_callback(
            '/@(style)(\( ( (?>[^()]+) | (?2) )* \))/x', function ($match) {
                if ($match[1] === 'style') {
                    $match[2] = str_replace('"', "'", $match[2]);

                    return ":style=\"\Illuminate\Support\Arr::toCssStyles{$match[2]}\"";
                }

                return $match[0];
            }, $attributeString
        );
    }

    /**
     * Parse the "bind" attributes in a given attribute string into their fully-qualified syntax.
     *
     * @param  string  $attributeString
     * @return string
     */
    protected function parseBindAttributes(string $attributeString)
    {
        $pattern = "/
            (?:^|\s+)     # start of the string or whitespace between attributes
            :(?!:)        # attribute needs to start with a single colon
            ([\w\-:.@]+)  # match the actual attribute name
            =             # only match attributes that have a value
        /xm";

        return preg_replace($pattern, ' bind:$1=', $attributeString);
    }

    /**
     * Compile any Blade echo statements that are present in the attribute string.
     *
     * These echo statements need to be converted to string concatenation statements.
     *
     * @param  string  $attributeString
     * @return string
     */
    protected function compileAttributeEchos(string $attributeString)
    {
        $value = $this->blade->compileEchos($attributeString);

        $value = $this->escapeSingleQuotesOutsideOfPhpBlocks($value);

        $value = str_replace('<?php echo ', '\'.', $value);
        $value = str_replace('; ?>', '.\'', $value);

        return $value;
    }

    /**
     * Escape the single quotes in the given string that are outside of PHP blocks.
     *
     * @param  string  $value
     * @return string
     */
    protected function escapeSingleQuotesOutsideOfPhpBlocks(string $value)
    {
        return (new Collection(token_get_all($value)))->map(function ($token) {
            if (! is_array($token)) {
                return $token;
            }

            return $token[0] === T_INLINE_HTML
                ? str_replace("'", "\\'", $token[1])
                : $token[1];
        })->implode('');
    }

    /**
     * Convert an array of attributes to a string.
     *
     * @param  array  $attributes
     * @param  bool  $escapeBound
     * @return string
     */
    protected function attributesToString(array $attributes, $escapeBound = true)
    {
        return (new Collection($attributes))
            ->map(function (string $value, string $attribute) use ($escapeBound) {
                return $escapeBound && isset($this->boundAttributes[$attribute]) && $value !== 'true' && ! is_numeric($value)
                    ? "'{$attribute}' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute({$value})"
                    : "'{$attribute}' => {$value}";
            })
            ->implode(',');
    }

    /**
     * Strip any quotes from the given string.
     *
     * @param  string  $value
     * @return string
     */
    public function stripQuotes(string $value)
    {
        return Str::startsWith($value, ['"', '\''])
            ? substr($value, 1, -1)
            : $value;
    }
}
