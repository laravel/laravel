<?php

namespace Illuminate\Validation\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FormatsMessages
{
    use ReplacesAttributes;

    /**
     * Get the validation message for an attribute and rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return string
     */
    protected function getMessage($attribute, $rule)
    {
        $attributeWithPlaceholders = $attribute;

        $attribute = $this->replacePlaceholderInString($attribute);

        $inlineMessage = $this->getInlineMessage($attribute, $rule);

        // First we will retrieve the custom message for the validation rule if one
        // exists. If a custom validation message is being used we'll return the
        // custom message, otherwise we'll keep searching for a valid message.
        if (! is_null($inlineMessage)) {
            return $inlineMessage;
        }

        $lowerRule = Str::snake($rule);

        $customKey = "validation.custom.{$attribute}.{$lowerRule}";

        $customMessage = $this->getCustomMessageFromTranslator(
            in_array($rule, $this->sizeRules)
                ? [$customKey.".{$this->getAttributeType($attribute)}", $customKey]
                : $customKey
        );

        // First we check for a custom defined validation message for the attribute
        // and rule. This allows the developer to specify specific messages for
        // only some attributes and rules that need to get specially formed.
        if ($customMessage !== $customKey) {
            return $customMessage;
        }

        // If the rule being validated is a "size" rule, we will need to gather the
        // specific error message for the type of attribute being validated such
        // as a number, file or string which all have different message types.
        elseif (in_array($rule, $this->sizeRules)) {
            return $this->getSizeMessage($attributeWithPlaceholders, $rule);
        }

        // Finally, if no developer specified messages have been set, and no other
        // special messages apply for this rule, we will just pull the default
        // messages out of the translator service for this validation rule.
        $key = "validation.{$lowerRule}";

        if ($key !== ($value = $this->translator->get($key))) {
            return $value;
        }

        return $this->getFromLocalArray(
            $attribute, $lowerRule, $this->fallbackMessages
        ) ?: $key;
    }

    /**
     * Get the proper inline error message for standard and size rules.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return string|null
     */
    protected function getInlineMessage($attribute, $rule)
    {
        $inlineEntry = $this->getFromLocalArray($attribute, Str::snake($rule));

        return is_array($inlineEntry) && in_array($rule, $this->sizeRules)
            ? ($inlineEntry[$this->getAttributeType($attribute)] ?? null)
            : $inlineEntry;
    }

    /**
     * Get the inline message for a rule if it exists.
     *
     * @param  string  $attribute
     * @param  string  $lowerRule
     * @param  array|null  $source
     * @return string|null
     */
    protected function getFromLocalArray($attribute, $lowerRule, $source = null)
    {
        $source = $source ?: $this->customMessages;

        $keys = ["{$attribute}.{$lowerRule}", $lowerRule, $attribute];

        if ($this->getAttributeType($attribute) !== 'file') {
            $shortRule = "{$attribute}.".Str::snake(class_basename($lowerRule));

            if (! in_array($shortRule, $keys)) {
                $keys[] = $shortRule;
            }
        }

        // First we will check for a custom message for an attribute specific rule
        // message for the fields, then we will check for a general custom line
        // that is not attribute specific. If we find either we'll return it.
        foreach ($keys as $key) {
            foreach (array_keys($source) as $sourceKey) {
                if (str_contains($sourceKey, '*')) {
                    $pattern = str_replace('\*', '([^.]*)', preg_quote($sourceKey, '#'));

                    if (preg_match('#^'.$pattern.'\z#u', $key) === 1) {
                        $message = $source[$sourceKey];

                        if (is_array($message)) {
                            return $message[$lowerRule] ?? null;
                        }

                        return $message;
                    }

                    continue;
                }

                if (Str::is($sourceKey, $key)) {
                    $message = $source[$sourceKey];

                    if ($sourceKey === $attribute && is_array($message)) {
                        return $message[$lowerRule] ?? null;
                    }

                    return $message;
                }
            }
        }
    }

    /**
     * Get the custom error message from the translator.
     *
     * @param  array|string  $keys
     * @return string
     */
    protected function getCustomMessageFromTranslator($keys)
    {
        foreach (Arr::wrap($keys) as $key) {
            if (($message = $this->translator->get($key)) !== $key) {
                return $message;
            }

            // If an exact match was not found for the key, we will collapse all of these
            // messages and loop through them and try to find a wildcard match for the
            // given key. Otherwise, we will simply return the key's value back out.
            $shortKey = preg_replace(
                '/^validation\.custom\./', '', $key
            );

            $message = $this->getWildcardCustomMessages(Arr::dot(
                (array) $this->translator->get('validation.custom')
            ), $shortKey, $key);

            if ($message !== $key) {
                return $message;
            }
        }

        return Arr::last(Arr::wrap($keys));
    }

    /**
     * Check the given messages for a wildcard key.
     *
     * @param  array  $messages
     * @param  string  $search
     * @param  string  $default
     * @return string
     */
    protected function getWildcardCustomMessages($messages, $search, $default)
    {
        foreach ($messages as $key => $message) {
            if ($search === $key || (Str::contains($key, ['*']) && Str::is($key, $search))) {
                return $message;
            }
        }

        return $default;
    }

    /**
     * Get the proper error message for an attribute and size rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return string
     */
    protected function getSizeMessage($attribute, $rule)
    {
        $lowerRule = Str::snake($rule);

        // There are three different types of size validations. The attribute may be
        // either a number, file, or string so we will check a few things to know
        // which type of value it is and return the correct line for that type.
        $type = $this->getAttributeType($attribute);

        $key = "validation.{$lowerRule}.{$type}";

        return $this->translator->get($key);
    }

    /**
     * Get the data type of the given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttributeType($attribute)
    {
        // We assume that the attributes present in the file array are files so that
        // means that if the attribute does not have a numeric rule and the files
        // list doesn't have it we'll just consider it a string by elimination.
        return match (true) {
            $this->hasRule($attribute, $this->numericRules) => 'numeric',
            $this->hasRule($attribute, ['Array', 'List']) => 'array',
            $this->getValue($attribute) instanceof UploadedFile,
            $this->getValue($attribute) instanceof File => 'file',
            default => 'string',
        };
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array  $parameters
     * @return string
     */
    public function makeReplacements($message, $attribute, $rule, $parameters)
    {
        $message = $this->replaceAttributePlaceholder(
            $message, $this->getDisplayableAttribute($attribute)
        );

        $message = $this->replaceInputPlaceholder($message, $attribute);
        $message = $this->replaceIndexPlaceholder($message, $attribute);
        $message = $this->replacePositionPlaceholder($message, $attribute);
        $message = $this->replaceOrdinalPositionPlaceholder($message, $attribute);

        if (isset($this->replacers[Str::snake($rule)])) {
            return $this->callReplacer($message, $attribute, Str::snake($rule), $parameters, $this);
        } elseif (method_exists($this, $replacer = "replace{$rule}")) {
            return $this->$replacer($message, $attribute, $rule, $parameters);
        }

        return $message;
    }

    /**
     * Get the displayable name of the attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    public function getDisplayableAttribute($attribute)
    {
        $primaryAttribute = $this->getPrimaryAttribute($attribute);

        $expectedAttributes = $attribute != $primaryAttribute
            ? [$attribute, $primaryAttribute]
            : [$attribute];

        foreach ($expectedAttributes as $name) {
            // The developer may dynamically specify the array of custom attributes on this
            // validator instance. If the attribute exists in this array it is used over
            // the other ways of pulling the attribute name for this given attributes.
            if ($inlineAttribute = $this->getAttributeFromLocalArray($name)) {
                return $inlineAttribute;
            }

            // We allow for a developer to specify language lines for any attribute in this
            // application, which allows flexibility for displaying a unique displayable
            // version of the attribute name instead of the name used in an HTTP POST.
            if ($translatedAttribute = $this->getAttributeFromTranslations($name)) {
                return $translatedAttribute;
            }
        }

        // When no language line has been specified for the attribute and it is also
        // an implicit attribute we will display the raw attribute's name and not
        // modify it with any of these replacements before we display the name.
        if (isset($this->implicitAttributes[$primaryAttribute])) {
            return ($formatter = $this->implicitAttributesFormatter)
                ? $formatter($attribute)
                : $attribute;
        }

        return str_replace('_', ' ', Str::snake($attribute));
    }

    /**
     * Get the given attribute from the attribute translations.
     *
     * @param  string  $name
     * @return string|null
     */
    protected function getAttributeFromTranslations($name)
    {
        if (! is_array($attributes = $this->translator->get('validation.attributes'))) {
            return null;
        }

        return $this->getAttributeFromLocalArray($name, Arr::dot($attributes));
    }

    /**
     * Get the custom name for an attribute if it exists in the given array.
     *
     * @param  string  $attribute
     * @param  array|null  $source
     * @return string|null
     */
    protected function getAttributeFromLocalArray($attribute, $source = null)
    {
        $source = $source ?: $this->customAttributes;

        if (isset($source[$attribute])) {
            return $source[$attribute];
        }

        foreach (array_keys($source) as $sourceKey) {
            if (str_contains($sourceKey, '*')) {
                $pattern = str_replace('\*', '([^.]*)', preg_quote($sourceKey, '#'));

                if (preg_match('#^'.$pattern.'\z#u', $attribute) === 1) {
                    return $source[$sourceKey];
                }
            }
        }
    }

    /**
     * Replace the :attribute placeholder in the given message.
     *
     * @param  string  $message
     * @param  string  $value
     * @return string
     */
    protected function replaceAttributePlaceholder($message, $value)
    {
        return str_replace(
            [':attribute', ':ATTRIBUTE', ':Attribute'],
            [$value, Str::upper($value), Str::ucfirst($value)],
            $message
        );
    }

    /**
     * Replace the :index placeholder in the given message.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @return string
     */
    protected function replaceIndexPlaceholder($message, $attribute)
    {
        return $this->replaceIndexOrPositionPlaceholder(
            $message, $attribute, 'index'
        );
    }

    /**
     * Replace the :position placeholder in the given message.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @return string
     */
    protected function replacePositionPlaceholder($message, $attribute)
    {
        return $this->replaceIndexOrPositionPlaceholder(
            $message, $attribute, 'position', fn ($segment) => $segment + 1
        );
    }

    /**
     * Replace the :ordinal-position placeholder in the given message.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @return string
     */
    protected function replaceOrdinalPositionPlaceholder($message, $attribute)
    {
        if (! extension_loaded('intl')) {
            return $message;
        }

        return $this->replaceIndexOrPositionPlaceholder(
            $message, $attribute, 'ordinal-position', fn ($segment) => Number::ordinal($segment + 1)
        );
    }

    /**
     * Replace the :index or :position placeholder in the given message.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $placeholder
     * @param  \Closure|null  $modifier
     * @return string
     */
    protected function replaceIndexOrPositionPlaceholder($message, $attribute, $placeholder, ?Closure $modifier = null)
    {
        if (! str_contains(strtolower($message), ':'.$placeholder) &&
            ! str_contains(strtolower($message), '-'.$placeholder)) {
            return $message;
        }

        $segments = explode('.', $attribute);

        $modifier ??= fn ($value) => $value;

        $numericIndex = 1;

        foreach ($segments as $segment) {
            if (is_numeric($segment)) {
                if ($numericIndex === 1) {
                    $message = str_ireplace(':'.$placeholder, $modifier((int) $segment), $message);
                }

                $message = str_ireplace(
                    ':'.$this->numberToIndexOrPositionWord($numericIndex).'-'.$placeholder,
                    $modifier((int) $segment),
                    $message
                );

                $numericIndex++;
            }
        }

        return $message;
    }

    /**
     * Get the word for a index or position segment.
     *
     * @param  int  $value
     * @return string
     */
    protected function numberToIndexOrPositionWord(int $value)
    {
        return [
            1 => 'first',
            2 => 'second',
            3 => 'third',
            4 => 'fourth',
            5 => 'fifth',
            6 => 'sixth',
            7 => 'seventh',
            8 => 'eighth',
            9 => 'ninth',
            10 => 'tenth',
        ][(int) $value] ?? 'other';
    }

    /**
     * Replace the :input placeholder in the given message.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @return string
     */
    protected function replaceInputPlaceholder($message, $attribute)
    {
        if (! str_contains($message, ':input')) {
            return $message;
        }

        $actualValue = $this->getValue($attribute);

        if (is_scalar($actualValue) || is_null($actualValue)) {
            $message = str_replace(':input', $this->getDisplayableValue($attribute, $actualValue), $message);
        }

        return $message;
    }

    /**
     * Get the displayable name of the value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return string
     */
    public function getDisplayableValue($attribute, $value)
    {
        if (isset($this->customValues[$attribute][$value])) {
            return $this->customValues[$attribute][$value];
        }

        if (is_array($value)) {
            return 'array';
        }

        $key = "validation.values.{$attribute}.{$value}";

        if (($line = $this->translator->get($key)) !== $key) {
            return $line;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'empty';
        }

        return (string) $value;
    }

    /**
     * Transform an array of attributes to their displayable form.
     *
     * @param  array  $values
     * @return array
     */
    protected function getAttributeList(array $values)
    {
        $attributes = [];

        // For each attribute in the list we will simply get its displayable form as
        // this is convenient when replacing lists of parameters like some of the
        // replacement functions do when formatting out the validation message.
        foreach ($values as $key => $value) {
            $attributes[$key] = $this->getDisplayableAttribute($value);
        }

        return $attributes;
    }

    /**
     * Call a custom validator message replacer.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array  $parameters
     * @param  \Illuminate\Validation\Validator  $validator
     * @return string|null
     */
    protected function callReplacer($message, $attribute, $rule, $parameters, $validator)
    {
        $callback = $this->replacers[$rule];

        if ($callback instanceof Closure) {
            return $callback(...func_get_args());
        } elseif (is_string($callback)) {
            return $this->callClassBasedReplacer($callback, $message, $attribute, $rule, $parameters, $validator);
        }
    }

    /**
     * Call a class based validator message replacer.
     *
     * @param  string  $callback
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array  $parameters
     * @param  \Illuminate\Validation\Validator  $validator
     * @return string
     */
    protected function callClassBasedReplacer($callback, $message, $attribute, $rule, $parameters, $validator)
    {
        [$class, $method] = Str::parseCallback($callback, 'replace');

        return $this->container->make($class)->{$method}(...array_slice(func_get_args(), 1));
    }
}
