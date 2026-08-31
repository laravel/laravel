<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesUseStatements
{
    /**
     * Compile the use statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileUse($expression)
    {
        $expression = trim(preg_replace('/[()]/', '', $expression), " '\"");

        // Isolate alias...
        if (str_contains($expression, '{')) {
            $pathWithOptionalModifier = $expression;
            $aliasWithLeadingSpace = '';
        } else {
            $segments = explode(',', $expression);
            $pathWithOptionalModifier = trim($segments[0], " '\"");

            $aliasWithLeadingSpace = isset($segments[1])
                ? ' as '.trim($segments[1], " '\"")
                : '';
        }

        // Split modifier and path...
        if (str_starts_with($pathWithOptionalModifier, 'function ')) {
            $modifierWithTrailingSpace = 'function ';
            $path = explode(' ', $pathWithOptionalModifier, 2)[1] ?? $pathWithOptionalModifier;
        } elseif (str_starts_with($pathWithOptionalModifier, 'const ')) {
            $modifierWithTrailingSpace = 'const ';
            $path = explode(' ', $pathWithOptionalModifier, 2)[1] ?? $pathWithOptionalModifier;
        } else {
            $modifierWithTrailingSpace = '';
            $path = $pathWithOptionalModifier;
        }

        $path = ltrim($path, '\\');

        return "<?php use {$modifierWithTrailingSpace}\\{$path}{$aliasWithLeadingSpace}; ?>";
    }
}
