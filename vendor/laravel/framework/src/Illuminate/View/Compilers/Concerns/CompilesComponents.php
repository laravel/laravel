<?php

namespace Illuminate\View\Compilers\Concerns;

use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Support\Str;
use Illuminate\View\AnonymousComponent;
use Illuminate\View\ComponentAttributeBag;

trait CompilesComponents
{
    /**
     * The component name hash stack.
     *
     * @var array
     */
    protected static $componentHashStack = [];

    /**
     * Compile the component statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileComponent($expression)
    {
        [$component, $alias, $data] = str_contains($expression, ',')
            ? array_map(trim(...), explode(',', trim($expression, '()'), 3)) + ['', '', '']
            : [trim($expression, '()'), '', ''];

        $component = trim($component, '\'"');

        $hash = static::newComponentHash(
            $component === AnonymousComponent::class ? $component.':'.trim($alias, '\'"') : $component
        );

        if (Str::contains($component, ['::class', '\\'])) {
            return static::compileClassComponentOpening($component, $alias, $data, $hash);
        }

        return "<?php \$__env->startComponent{$expression}; ?>";
    }

    /**
     * Get a new component hash for a component name.
     *
     * @param  string  $component
     * @return string
     */
    public static function newComponentHash(string $component)
    {
        static::$componentHashStack[] = $hash = hash('xxh128', $component);

        return $hash;
    }

    /**
     * Compile a class component opening.
     *
     * @param  string  $component
     * @param  string  $alias
     * @param  string  $data
     * @param  string  $hash
     * @return string
     */
    public static function compileClassComponentOpening(string $component, string $alias, string $data, string $hash)
    {
        return implode("\n", [
            '<?php if (isset($component)) { $__componentOriginal'.$hash.' = $component; } ?>',
            '<?php if (isset($attributes)) { $__attributesOriginal'.$hash.' = $attributes; } ?>',
            '<?php $component = '.$component.'::resolve('.($data ?: '[]').' + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>',
            '<?php $component->withName('.$alias.'); ?>',
            '<?php if ($component->shouldRender()): ?>',
            '<?php $__env->startComponent($component->resolveView(), $component->data()); ?>',
        ]);
    }

    /**
     * Compile the end-component statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndComponent()
    {
        return '<?php echo $__env->renderComponent(); ?>';
    }

    /**
     * Compile the end-component statements into valid PHP.
     *
     * @return string
     */
    public function compileEndComponentClass()
    {
        $hash = array_pop(static::$componentHashStack);

        return $this->compileEndComponent()."\n".implode("\n", [
            '<?php endif; ?>',
            '<?php if (isset($__attributesOriginal'.$hash.')): ?>',
            '<?php $attributes = $__attributesOriginal'.$hash.'; ?>',
            '<?php unset($__attributesOriginal'.$hash.'); ?>',
            '<?php endif; ?>',
            '<?php if (isset($__componentOriginal'.$hash.')): ?>',
            '<?php $component = $__componentOriginal'.$hash.'; ?>',
            '<?php unset($__componentOriginal'.$hash.'); ?>',
            '<?php endif; ?>',
        ]);
    }

    /**
     * Compile the slot statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileSlot($expression)
    {
        return "<?php \$__env->slot{$expression}; ?>";
    }

    /**
     * Compile the end-slot statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndSlot()
    {
        return '<?php $__env->endSlot(); ?>';
    }

    /**
     * Compile the component-first statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileComponentFirst($expression)
    {
        return "<?php \$__env->startComponentFirst{$expression}; ?>";
    }

    /**
     * Compile the end-component-first statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndComponentFirst()
    {
        return $this->compileEndComponent();
    }

    /**
     * Compile the prop statement into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileProps($expression)
    {
        return "<?php \$attributes ??= new \\Illuminate\\View\\ComponentAttributeBag;

\$__newAttributes = [];
\$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames({$expression});

foreach (\$attributes->all() as \$__key => \$__value) {
    if (in_array(\$__key, \$__propNames)) {
        \$\$__key = \$\$__key ?? \$__value;
    } else {
        \$__newAttributes[\$__key] = \$__value;
    }
}

\$attributes = new \Illuminate\View\ComponentAttributeBag(\$__newAttributes);

unset(\$__propNames);
unset(\$__newAttributes);

foreach (array_filter({$expression}, 'is_string', ARRAY_FILTER_USE_KEY) as \$__key => \$__value) {
    \$\$__key = \$\$__key ?? \$__value;
}

\$__defined_vars = get_defined_vars();

foreach (\$attributes->all() as \$__key => \$__value) {
    if (array_key_exists(\$__key, \$__defined_vars)) unset(\$\$__key);
}

unset(\$__defined_vars, \$__key, \$__value); ?>";
    }

    /**
     * Compile the aware statement into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileAware($expression)
    {
        return "<?php foreach ({$expression} as \$__key => \$__value) {
    \$__consumeVariable = is_string(\$__key) ? \$__key : \$__value;
    \$\$__consumeVariable = is_string(\$__key) ? \$__env->getConsumableComponentData(\$__key, \$__value) : \$__env->getConsumableComponentData(\$__value);
} ?>";
    }

    /**
     * Sanitize the given component attribute value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function sanitizeComponentAttribute($value)
    {
        if ($value instanceof CanBeEscapedWhenCastToString) {
            return $value->escapeWhenCastingToString();
        }

        return is_string($value) ||
            (is_object($value) && ! $value instanceof ComponentAttributeBag && method_exists($value, '__toString'))
                ? e($value)
                : $value;
    }
}
