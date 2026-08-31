<?php

namespace Illuminate\View;

use BackedEnum;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\ComponentTagCompiler;

use function Illuminate\Support\enum_value;

class DynamicComponent extends Component
{
    /**
     * The name of the component.
     *
     * @var string
     */
    public $component;

    /**
     * The component tag compiler instance.
     *
     * @var \Illuminate\View\Compilers\BladeTagCompiler
     */
    protected static $compiler;

    /**
     * The cached component classes.
     *
     * @var array
     */
    protected static $componentClasses = [];

    /**
     * Create a new component instance.
     *
     * @param  \BackedEnum|string  $component
     */
    public function __construct(BackedEnum|string $component)
    {
        $this->component = (string) enum_value($component);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $template = <<<'EOF'
<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
{{ props }}
<x-{{ component }} {{ bindings }} {{ attributes }}>
{{ slots }}
{{ defaultSlot }}
</x-{{ component }}>
EOF;

        return function ($data) use ($template) {
            $bindings = $this->bindings($class = $this->classForComponent());

            return str_replace(
                [
                    '{{ component }}',
                    '{{ props }}',
                    '{{ bindings }}',
                    '{{ attributes }}',
                    '{{ slots }}',
                    '{{ defaultSlot }}',
                ],
                [
                    $this->component,
                    $this->compileProps($bindings),
                    $this->compileBindings($bindings),
                    class_exists($class) ? '{{ $attributes }}' : '',
                    $this->compileSlots($data['__laravel_slots']),
                    '{{ $slot ?? "" }}',
                ],
                $template
            );
        };
    }

    /**
     * Compile the @props directive for the component.
     *
     * @param  array  $bindings
     * @return string
     */
    protected function compileProps(array $bindings)
    {
        if (empty($bindings)) {
            return '';
        }

        return '@props('.'[\''.implode('\',\'', (new Collection($bindings))->map(function ($dataKey) {
            return Str::camel($dataKey);
        })->all()).'\']'.')';
    }

    /**
     * Compile the bindings for the component.
     *
     * @param  array  $bindings
     * @return string
     */
    protected function compileBindings(array $bindings)
    {
        return (new Collection($bindings))
            ->map(fn ($key) => ':'.$key.'="$'.Str::camel(str_replace([':', '.'], ' ', $key)).'"')
            ->implode(' ');
    }

    /**
     * Compile the slots for the component.
     *
     * @param  array  $slots
     * @return string
     */
    protected function compileSlots(array $slots)
    {
        return (new Collection($slots))
            ->map(fn ($slot, $name) => $name === '__default' ? null : '<x-slot name="'.$name.'" '.((string) $slot->attributes).'>{{ $'.$name.' }}</x-slot>')
            ->filter()
            ->implode(PHP_EOL);
    }

    /**
     * Get the class for the current component.
     *
     * @return string
     */
    protected function classForComponent()
    {
        if (isset(static::$componentClasses[$this->component])) {
            return static::$componentClasses[$this->component];
        }

        return static::$componentClasses[$this->component] =
                    $this->compiler()->componentClass($this->component);
    }

    /**
     * Get the names of the variables that should be bound to the component.
     *
     * @param  string  $class
     * @return array
     */
    protected function bindings(string $class)
    {
        [$data] = $this->compiler()->partitionDataAndAttributes($class, $this->attributes->getAttributes());

        return array_keys($data->all());
    }

    /**
     * Get an instance of the Blade tag compiler.
     *
     * @return \Illuminate\View\Compilers\ComponentTagCompiler
     */
    protected function compiler()
    {
        if (! static::$compiler) {
            static::$compiler = new ComponentTagCompiler(
                Container::getInstance()->make('blade.compiler')->getClassComponentAliases(),
                Container::getInstance()->make('blade.compiler')->getClassComponentNamespaces(),
                Container::getInstance()->make('blade.compiler')
            );
        }

        return static::$compiler;
    }
}
