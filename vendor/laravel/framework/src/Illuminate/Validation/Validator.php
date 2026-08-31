<?php

namespace Illuminate\Validation;

use BadMethodCallException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ValidatedInput;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Validator implements ValidatorContract
{
    use Concerns\FormatsMessages,
        Concerns\ValidatesAttributes;

    /**
     * The Translator implementation.
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The Presence Verifier implementation.
     *
     * @var \Illuminate\Validation\PresenceVerifierInterface
     */
    protected $presenceVerifier;

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = [];

    /**
     * Attributes that should be excluded from the validated data.
     *
     * @var array
     */
    protected $excludeAttributes = [];

    /**
     * The message bag instance.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $messages;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The initial rules provided.
     *
     * @var array
     */
    protected $initialRules;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * The current rule that is validating.
     *
     * @var string
     */
    protected $currentRule;

    /**
     * The array of wildcard attributes with their asterisks expanded.
     *
     * @var array
     */
    protected $implicitAttributes = [];

    /**
     * The callback that should be used to format the attribute.
     *
     * @var callable|null
     */
    protected $implicitAttributesFormatter;

    /**
     * The cached data for the "distinct" rule.
     *
     * @var array
     */
    protected $distinctValues = [];

    /**
     * All of the registered "after" callbacks.
     *
     * @var array
     */
    protected $after = [];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public $customMessages = [];

    /**
     * The array of fallback error messages.
     *
     * @var array
     */
    public $fallbackMessages = [];

    /**
     * The array of custom attribute names.
     *
     * @var array
     */
    public $customAttributes = [];

    /**
     * The array of custom displayable values.
     *
     * @var array
     */
    public $customValues = [];

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = false;

    /**
     * Indicates that unvalidated array keys should be excluded, even if the parent array was validated.
     *
     * @var bool
     */
    public $excludeUnvalidatedArrayKeys = false;

    /**
     * All of the custom validator extensions.
     *
     * @var array
     */
    public $extensions = [];

    /**
     * All of the custom replacer extensions.
     *
     * @var array
     */
    public $replacers = [];

    /**
     * The validation rules that may be applied to files.
     *
     * @var string[]
     */
    protected $fileRules = [
        'Between',
        'Dimensions',
        'Encoding',
        'Extensions',
        'File',
        'Image',
        'Max',
        'Mimes',
        'Mimetypes',
        'Min',
        'Size',
    ];

    /**
     * The validation rules that imply the field is required.
     *
     * @var string[]
     */
    protected $implicitRules = [
        'Accepted',
        'AcceptedIf',
        'Declined',
        'DeclinedIf',
        'Filled',
        'Missing',
        'MissingIf',
        'MissingUnless',
        'MissingWith',
        'MissingWithAll',
        'Present',
        'PresentIf',
        'PresentUnless',
        'PresentWith',
        'PresentWithAll',
        'Required',
        'RequiredIf',
        'RequiredIfAccepted',
        'RequiredIfDeclined',
        'RequiredUnless',
        'RequiredWith',
        'RequiredWithAll',
        'RequiredWithout',
        'RequiredWithoutAll',
    ];

    /**
     * The validation rules which depend on other fields as parameters.
     *
     * @var string[]
     */
    protected $dependentRules = [
        'After',
        'AfterOrEqual',
        'Before',
        'BeforeOrEqual',
        'Confirmed',
        'Different',
        'ExcludeIf',
        'ExcludeUnless',
        'ExcludeWith',
        'ExcludeWithout',
        'Gt',
        'Gte',
        'Lt',
        'Lte',
        'AcceptedIf',
        'DeclinedIf',
        'RequiredIf',
        'RequiredIfAccepted',
        'RequiredIfDeclined',
        'RequiredUnless',
        'RequiredWith',
        'RequiredWithAll',
        'RequiredWithout',
        'RequiredWithoutAll',
        'PresentIf',
        'PresentUnless',
        'PresentWith',
        'PresentWithAll',
        'Prohibited',
        'ProhibitedIf',
        'ProhibitedIfAccepted',
        'ProhibitedIfDeclined',
        'ProhibitedUnless',
        'Prohibits',
        'MissingIf',
        'MissingUnless',
        'MissingWith',
        'MissingWithAll',
        'Same',
        'Unique',
    ];

    /**
     * The validation rules that can exclude an attribute.
     *
     * @var string[]
     */
    protected $excludeRules = ['Exclude', 'ExcludeIf', 'ExcludeUnless', 'ExcludeWith', 'ExcludeWithout'];

    /**
     * The size related validation rules.
     *
     * @var string[]
     */
    protected $sizeRules = ['Size', 'Between', 'Min', 'Max', 'Gt', 'Lt', 'Gte', 'Lte'];

    /**
     * The numeric related validation rules.
     *
     * @var string[]
     */
    protected $numericRules = ['Numeric', 'Integer', 'Decimal'];

    /**
     * The default numeric related validation rules.
     *
     * @var string[]
     */
    protected $defaultNumericRules = ['Numeric', 'Integer', 'Decimal'];

    /**
     * The current random hash for the validator.
     *
     * @var string|null
     */
    protected static $placeholderHash;

    /**
     * The exception to throw upon failure.
     *
     * @var class-string<\Illuminate\Validation\ValidationException>
     */
    protected $exception = ValidationException::class;

    /**
     * The custom callback to determine if an exponent is within allowed range.
     *
     * @var callable|null
     */
    protected $ensureExponentWithinAllowedRangeUsing;

    /**
     * Create a new Validator instance.
     *
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     */
    public function __construct(
        Translator $translator,
        array $data,
        array $rules,
        array $messages = [],
        array $attributes = [],
    ) {
        if (! isset(static::$placeholderHash)) {
            static::$placeholderHash = Str::random();
        }

        $this->initialRules = $rules;
        $this->translator = $translator;
        $this->customMessages = $messages;
        $this->data = $this->parseData($data);
        $this->customAttributes = $attributes;

        $this->setRules($rules);
    }

    /**
     * Parse the data array, converting dots and asterisks.
     *
     * @param  array  $data
     * @return array
     */
    public function parseData(array $data)
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->parseData($value);
            }

            $key = str_replace(
                ['.', '*'],
                ['__dot__'.static::$placeholderHash, '__asterisk__'.static::$placeholderHash],
                $key
            );

            $newData[$key] = $value;
        }

        return $newData;
    }

    /**
     * Replace the placeholders used in data keys.
     *
     * @param  array  $data
     * @return array
     */
    protected function replacePlaceholders($data)
    {
        $originalData = [];

        foreach ($data as $key => $value) {
            $originalData[$this->replacePlaceholderInString($key)] = is_array($value)
                ? $this->replacePlaceholders($value)
                : $value;
        }

        return $originalData;
    }

    /**
     * Replace the placeholders in the given string.
     *
     * @param  string  $value
     * @return string
     */
    protected function replacePlaceholderInString(string $value)
    {
        return str_replace(
            ['__dot__'.static::$placeholderHash, '__asterisk__'.static::$placeholderHash],
            ['.', '*'],
            $value
        );
    }

    /**
     * Replace each field parameter dot placeholder with dot.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function replaceDotPlaceholderInParameters(array $parameters)
    {
        return array_map(function ($field) {
            return str_replace('__dot__'.static::$placeholderHash, '.', $field);
        }, $parameters);
    }

    /**
     * Add an after validation callback.
     *
     * @param  callable|array|string  $callback
     * @return $this
     */
    public function after($callback)
    {
        if (is_array($callback) && ! is_callable($callback)) {
            foreach ($callback as $rule) {
                $this->after(method_exists($rule, 'after') ? $rule->after(...) : $rule);
            }

            return $this;
        }

        $this->after[] = fn () => $callback($this);

        return $this;
    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes()
    {
        $this->messages = new MessageBag;

        [$this->distinctValues, $this->failedRules] = [[], []];

        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);

                continue;
            }

            if ($this->stopOnFirstFailure && $this->messages->isNotEmpty()) {
                break;
            }

            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);

                if ($this->shouldBeExcluded($attribute)) {
                    break;
                }

                if ($this->shouldStopValidating($attribute)) {
                    break;
                }
            }
        }

        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);
            }
        }

        // Here we will spin through all of the "after" hooks on this validator and
        // fire them off. This gives the callbacks a chance to perform all kinds
        // of other validation that needs to get wrapped up in this operation.
        foreach ($this->after as $after) {
            $after();
        }

        return $this->messages->isEmpty();
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails()
    {
        return ! $this->passes();
    }

    /**
     * Execute the callback if the data passes the validation rules.
     *
     * @template TWhenReturnType
     *
     * @param  (callable($this): TWhenReturnType)  $callback
     * @param  (callable($this): TWhenReturnType)|null  $default
     * @return $this|TWhenReturnType
     */
    public function whenPasses(callable $callback, ?callable $default = null)
    {
        if ($this->passes()) {
            return $callback($this) ?? $this;
        } elseif ($default) {
            return $default($this) ?? $this;
        }

        return $this;
    }

    /**
     * Execute the callback if the data fails the validation rules.
     *
     * @template TWhenReturnType
     *
     * @param  (callable($this): TWhenReturnType)  $callback
     * @param  (callable($this): TWhenReturnType)|null  $default
     * @return $this|TWhenReturnType
     */
    public function whenFails(callable $callback, ?callable $default = null)
    {
        if ($this->fails()) {
            return $callback($this) ?? $this;
        } elseif ($default) {
            return $default($this) ?? $this;
        }

        return $this;
    }

    /**
     * Determine if the attribute should be excluded.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function shouldBeExcluded($attribute)
    {
        foreach ($this->excludeAttributes as $excludeAttribute) {
            if ($attribute === $excludeAttribute ||
                Str::startsWith($attribute, $excludeAttribute.'.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove the given attribute.
     *
     * @param  string  $attribute
     * @return void
     */
    protected function removeAttribute($attribute)
    {
        Arr::forget($this->data, $attribute);
        Arr::forget($this->rules, $attribute);
    }

    /**
     * Run the validator's rules against its data.
     *
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate()
    {
        throw_if($this->fails(), $this->exception, $this);

        return $this->validated();
    }

    /**
     * Run the validator's rules against its data.
     *
     * @param  string  $errorBag
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateWithBag(string $errorBag)
    {
        try {
            return $this->validate();
        } catch (ValidationException $e) {
            $e->errorBag = $errorBag;

            throw $e;
        }
    }

    /**
     * Get a validated input container for the validated input.
     *
     * @param  array|null  $keys
     * @return \Illuminate\Support\ValidatedInput|array
     */
    public function safe(?array $keys = null)
    {
        return is_array($keys)
            ? (new ValidatedInput($this->validated()))->only($keys)
            : new ValidatedInput($this->validated());
    }

    /**
     * Get the attributes and values that were validated.
     *
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated()
    {
        if (! $this->messages) {
            $this->passes();
        }

        throw_if($this->messages->isNotEmpty(), $this->exception, $this);

        $results = [];

        $missingValue = new stdClass;

        foreach ($this->getRules() as $key => $rules) {
            $value = data_get($this->getData(), $key, $missingValue);

            if ($this->excludeUnvalidatedArrayKeys &&
                (in_array('array', $rules) || in_array('list', $rules)) &&
                $value !== null &&
                ! empty(preg_grep('/^'.preg_quote($key, '/').'\.+/', array_keys($this->getRules())))) {
                continue;
            }

            if ($value !== $missingValue) {
                Arr::set($results, $key, $value);
            }
        }

        return $this->replacePlaceholders($results);
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return void
     */
    protected function validateAttribute($attribute, $rule)
    {
        $this->currentRule = $rule;

        [$rule, $parameters] = ValidationRuleParser::parse($rule);

        if ($rule === '') {
            return;
        }

        // First we will get the correct keys for the given attribute in case the field is nested in
        // an array. Then we determine if the given rule accepts other field names as parameters.
        // If so, we will replace any asterisks found in the parameters with the correct keys.
        if ($this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceDotInParameters($parameters);

            if ($keys = $this->getExplicitKeys($attribute)) {
                $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
            }
        }

        $value = $this->getValue($attribute);

        // If the attribute is a file, we will verify that the file upload was actually successful
        // and if it wasn't we will add a failure for the attribute. Files may not successfully
        // upload if they are too large based on PHP's settings so we will bail in this case.
        if ($value instanceof UploadedFile && ! $value->isValid() &&
            $this->hasRule($attribute, array_merge($this->fileRules, $this->implicitRules))
        ) {
            return $this->addFailure($attribute, 'uploaded', []);
        }

        // If we have made it this far we will make sure the attribute is validatable and if it is
        // we will call the validation method with the attribute. If a method returns false the
        // attribute is invalid and we will add a failure message for this failing attribute.
        $validatable = $this->isValidatable($rule, $attribute, $value);

        if ($rule instanceof RuleContract) {
            return $validatable
                ? $this->validateUsingCustomRule($attribute, $value, $rule)
                : null;
        }

        $method = "validate{$rule}";

        $this->numericRules = $this->defaultNumericRules;

        if ($validatable && ! $this->$method($attribute, $value, $parameters, $this)) {
            $this->addFailure($attribute, $rule, $parameters);
        }
    }

    /**
     * Determine if the given rule depends on other fields.
     *
     * @param  string  $rule
     * @return bool
     */
    protected function dependsOnOtherFields($rule)
    {
        return in_array($rule, $this->dependentRules);
    }

    /**
     * Get the explicit keys from an attribute flattened with dot notation.
     *
     * E.g. 'foo.1.bar.spark.baz' -> [1, 'spark'] for 'foo.*.bar.*.baz'
     *
     * @param  string  $attribute
     * @return array
     */
    protected function getExplicitKeys($attribute)
    {
        $pattern = str_replace('\*', '([^\.]+)', preg_quote($this->getPrimaryAttribute($attribute), '/'));

        if (preg_match('/^'.$pattern.'/', $attribute, $keys)) {
            array_shift($keys);

            return $keys;
        }

        return [];
    }

    /**
     * Get the primary attribute name.
     *
     * For example, if "name.0" is given, "name.*" will be returned.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getPrimaryAttribute($attribute)
    {
        foreach ($this->implicitAttributes as $unparsed => $parsed) {
            if (in_array($attribute, $parsed, true)) {
                return $unparsed;
            }
        }

        return $attribute;
    }

    /**
     * Replace each field parameter which has an escaped dot with the dot placeholder.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function replaceDotInParameters(array $parameters)
    {
        return array_map(function ($field) {
            return static::encodeAttributeWithPlaceholder((string) ($field ?? ''));
        }, $parameters);
    }

    /**
     * Replace each field parameter which has asterisks with the given keys.
     *
     * @param  array  $parameters
     * @param  array  $keys
     * @return array
     */
    protected function replaceAsterisksInParameters(array $parameters, array $keys)
    {
        return array_map(function ($field) use ($keys) {
            return vsprintf(str_replace('*', '%s', $field), $keys);
        }, $parameters);
    }

    /**
     * Determine if the attribute is validatable.
     *
     * @param  object|string  $rule
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    protected function isValidatable($rule, $attribute, $value)
    {
        if (in_array($rule, $this->excludeRules)) {
            return true;
        }

        return $this->presentOrRuleIsImplicit($rule, $attribute, $value) &&
               $this->passesOptionalCheck($attribute) &&
               $this->isNotNullIfMarkedAsNullable($rule, $attribute) &&
               $this->hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute);
    }

    /**
     * Determine if the field is present, or the rule implies required.
     *
     * @param  object|string  $rule
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    protected function presentOrRuleIsImplicit($rule, $attribute, $value)
    {
        if (is_string($value) && trim($value) === '') {
            return $this->isImplicit($rule);
        }

        return $this->validatePresent($attribute, $value) ||
               $this->isImplicit($rule);
    }

    /**
     * Determine if a given rule implies the attribute is required.
     *
     * @param  object|string  $rule
     * @return bool
     */
    protected function isImplicit($rule)
    {
        return $rule instanceof ImplicitRule ||
               in_array($rule, $this->implicitRules);
    }

    /**
     * Determine if the attribute passes any optional check.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function passesOptionalCheck($attribute)
    {
        if (! $this->hasRule($attribute, ['Sometimes'])) {
            return true;
        }

        $data = ValidationData::initializeAndGatherData($attribute, $this->data);

        return array_key_exists($attribute, $data)
            || array_key_exists($attribute, $this->data);
    }

    /**
     * Determine if the attribute fails the nullable check.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @return bool
     */
    protected function isNotNullIfMarkedAsNullable($rule, $attribute)
    {
        if ($this->isImplicit($rule) || ! $this->hasRule($attribute, ['Nullable'])) {
            return true;
        }

        return ! is_null(Arr::get($this->data, $attribute, 0));
    }

    /**
     * Determine if it's a necessary presence validation.
     *
     * This is to avoid possible database type comparison errors.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @return bool
     */
    protected function hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute)
    {
        return in_array($rule, ['Unique', 'Exists']) ? ! $this->messages->has($attribute) : true;
    }

    /**
     * Validate an attribute using a custom rule object.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Illuminate\Contracts\Validation\Rule  $rule
     * @return void
     */
    protected function validateUsingCustomRule($attribute, $value, $rule)
    {
        $originalAttribute = $this->replacePlaceholderInString($attribute);

        $attribute = match (true) {
            $rule instanceof Rules\Email => $attribute,
            $rule instanceof Rules\File => $attribute,
            $rule instanceof Rules\Password => $attribute,
            default => $originalAttribute,
        };

        $value = is_array($value) ? $this->replacePlaceholders($value) : $value;

        if ($rule instanceof ValidatorAwareRule) {
            if ($attribute !== $originalAttribute) {
                $this->addCustomAttributes([
                    $attribute => $this->customAttributes[$originalAttribute] ?? $originalAttribute,
                ]);
            }

            $rule->setValidator($this);
        }

        if ($rule instanceof DataAwareRule) {
            $rule->setData($this->data);
        }

        if (! $rule->passes($attribute, $value)) {
            $ruleClass = $rule instanceof InvokableValidationRule ?
                get_class($rule->invokable()) :
                get_class($rule);

            $this->failedRules[$originalAttribute][$ruleClass] = [];

            $messages = $this->getFromLocalArray($originalAttribute, $ruleClass) ?? $rule->message();

            $messages = $messages ? (array) $messages : [$ruleClass];

            foreach ($messages as $key => $message) {
                $key = is_string($key) ? $key : $originalAttribute;

                $this->messages->add($key, $this->makeReplacements(
                    $message, $key, $ruleClass, []
                ));
            }
        }
    }

    /**
     * Check if we should stop further validations on a given attribute.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function shouldStopValidating($attribute)
    {
        $cleanedAttribute = $this->replacePlaceholderInString($attribute);

        if ($this->hasRule($attribute, ['Bail'])) {
            return $this->messages->has($cleanedAttribute);
        }

        if (isset($this->failedRules[$cleanedAttribute]) &&
            array_key_exists('uploaded', $this->failedRules[$cleanedAttribute])) {
            return true;
        }

        // In case the attribute has any rule that indicates that the field is required
        // and that rule already failed then we should stop validation at this point
        // as now there is no point in calling other rules with this field empty.
        return $this->hasRule($attribute, $this->implicitRules) &&
               isset($this->failedRules[$cleanedAttribute]) &&
               array_intersect(array_keys($this->failedRules[$cleanedAttribute]), $this->implicitRules);
    }

    /**
     * Add a failed rule and error message to the collection.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array  $parameters
     * @return void
     */
    public function addFailure($attribute, $rule, $parameters = [])
    {
        if (! $this->messages) {
            $this->passes();
        }

        $attributeWithPlaceholders = $attribute;

        $attribute = $this->replacePlaceholderInString($attribute);

        if (in_array($rule, $this->excludeRules)) {
            return $this->excludeAttribute($attribute);
        }

        if ($this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceDotPlaceholderInParameters($parameters);
        }

        $this->messages->add($attribute, $this->makeReplacements(
            $this->getMessage($attributeWithPlaceholders, $rule), $attribute, $rule, $parameters
        ));

        $this->failedRules[$attribute][$rule] = $parameters;
    }

    /**
     * Add the given attribute to the list of excluded attributes.
     *
     * @param  string  $attribute
     * @return void
     */
    protected function excludeAttribute(string $attribute)
    {
        $this->excludeAttributes[] = $attribute;

        $this->excludeAttributes = array_unique($this->excludeAttributes);
    }

    /**
     * Returns the data which was valid.
     *
     * @return array
     */
    public function valid()
    {
        if (! $this->messages) {
            $this->passes();
        }

        return array_diff_key(
            $this->data, $this->attributesThatHaveMessages()
        );
    }

    /**
     * Returns the data which was invalid.
     *
     * @return array
     */
    public function invalid()
    {
        if (! $this->messages) {
            $this->passes();
        }

        $invalid = array_intersect_key(
            $this->data, $this->attributesThatHaveMessages()
        );

        $result = [];

        $failed = Arr::only(Arr::dot($invalid), array_keys($this->failed()));

        foreach ($failed as $key => $failure) {
            Arr::set($result, $key, $failure);
        }

        return $result;
    }

    /**
     * Generate an array of all attributes that have messages.
     *
     * @return array
     */
    protected function attributesThatHaveMessages()
    {
        return (new Collection($this->messages()->toArray()))
            ->map(fn ($message, $key) => explode('.', $key)[0])
            ->unique()
            ->flip()
            ->all();
    }

    /**
     * Get the failed validation rules.
     *
     * @return array
     */
    public function failed()
    {
        return $this->failedRules;
    }

    /**
     * Get the message container for the validator.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function messages()
    {
        if (! $this->messages) {
            $this->passes();
        }

        return $this->messages;
    }

    /**
     * An alternative more semantic shortcut to the message container.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->messages();
    }

    /**
     * Get the messages for the instance.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->messages();
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     *
     * @param  string  $attribute
     * @param  string|array  $rules
     * @return bool
     */
    public function hasRule($attribute, $rules)
    {
        return ! is_null($this->getRule($attribute, $rules));
    }

    /**
     * Get a rule and its parameters for a given attribute.
     *
     * @param  string  $attribute
     * @param  string|array  $rules
     * @return array|null
     */
    protected function getRule($attribute, $rules)
    {
        if (! array_key_exists($attribute, $this->rules)) {
            return;
        }

        $rules = (array) $rules;

        foreach ($this->rules[$attribute] as $rule) {
            [$rule, $parameters] = ValidationRuleParser::parse($rule);

            if (in_array($rule, $rules)) {
                return [$rule, $parameters];
            }
        }
    }

    /**
     * Get the data under validation.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->getData();
    }

    /**
     * Get the data under validation.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $this->parseData($data);

        $this->setRules($this->initialRules);

        return $this;
    }

    /**
     * Get the value of a given attribute.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function getValue($attribute)
    {
        return Arr::get($this->data, $attribute);
    }

    /**
     * Set the value of a given attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return void
     */
    public function setValue($attribute, $value)
    {
        Arr::set($this->data, $attribute, $value);
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Get the validation rules with key placeholders removed.
     *
     * @return array
     */
    public function getRulesWithoutPlaceholders()
    {
        return (new Collection($this->rules))
            ->mapWithKeys(fn ($value, $key) => [
                static::decodeAttributeWithPlaceholder($key) => $value,
            ])
            ->all();
    }

    /**
     * Set the validation rules.
     *
     * @param  array  $rules
     * @return $this
     */
    public function setRules(array $rules)
    {
        $rules = (new Collection($rules))
            ->mapWithKeys(function ($value, $key) {
                return [static::encodeAttributeWithPlaceholder($key) => $value];
            })
            ->toArray();

        $this->initialRules = $rules;

        $this->rules = [];

        $this->addRules($rules);

        return $this;
    }

    /**
     * Append new validation rules to the validator.
     *
     * @param  array  $rules
     * @return $this
     */
    public function appendRules(array $rules)
    {
        $rules = (new Collection($rules))
            ->map(function ($value) {
                return is_string($value) ? explode('|', $value) : $value;
            })
            ->all();

        return $this->setRules(array_merge_recursive($this->getRulesWithoutPlaceholders(), $rules));
    }

    /**
     * Parse the given rules and merge them into current rules.
     *
     * @internal
     *
     * @param  array  $rules
     * @return void
     */
    public function addRules($rules)
    {
        // The primary purpose of this parser is to expand any "*" rules to the all
        // of the explicit rules needed for the given data. For example the rule
        // names.* would get expanded to names.0, names.1, etc. for this data.
        $response = (new ValidationRuleParser($this->data))
            ->explode(ValidationRuleParser::filterConditionalRules($rules, $this->data));

        foreach ($response->rules as $key => $rule) {
            $this->rules[$key] = array_merge($this->rules[$key] ?? [], $rule);
        }

        $this->implicitAttributes = array_merge(
            $this->implicitAttributes, $response->implicitAttributes
        );
    }

    /**
     * Add conditions to a given field based on a Closure.
     *
     * @param  string|array  $attribute
     * @param  string|array  $rules
     * @param  callable  $callback
     * @return $this
     */
    public function sometimes($attribute, $rules, callable $callback)
    {
        $payload = new Fluent($this->data);

        foreach ((array) $attribute as $key) {
            $response = (new ValidationRuleParser($this->data))->explode([$key => $rules]);

            $this->implicitAttributes = array_merge($response->implicitAttributes, $this->implicitAttributes);

            foreach ($response->rules as $ruleKey => $ruleValue) {
                if ($callback($payload, $this->dataForSometimesIteration($ruleKey, ! str_ends_with($key, '.*')))) {
                    $this->addRules([static::encodeAttributeWithPlaceholder($ruleKey) => $ruleValue]);
                }
            }
        }

        return $this;
    }

    /**
     * Get the data that should be injected into the iteration of a wildcard "sometimes" callback.
     *
     * @param  string  $attribute
     * @param  bool  $removeLastSegmentOfAttribute
     * @return \Illuminate\Support\Fluent|mixed
     */
    private function dataForSometimesIteration(string $attribute, bool $removeLastSegmentOfAttribute)
    {
        $lastSegmentOfAttribute = strrchr($attribute, '.');

        $attribute = $lastSegmentOfAttribute && $removeLastSegmentOfAttribute
            ? Str::replaceLast($lastSegmentOfAttribute, '', $attribute)
            : $attribute;

        return is_array($data = data_get($this->data, $attribute))
            ? new Fluent($data)
            : $data;
    }

    /**
     * Instruct the validator to stop validating after the first rule failure.
     *
     * @param  bool  $stopOnFirstFailure
     * @return $this
     */
    public function stopOnFirstFailure($stopOnFirstFailure = true)
    {
        $this->stopOnFirstFailure = $stopOnFirstFailure;

        return $this;
    }

    /**
     * Register an array of custom validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addExtensions(array $extensions)
    {
        if ($extensions) {
            $keys = array_map(Str::snake(...), array_keys($extensions));

            $extensions = array_combine($keys, array_values($extensions));
        }

        $this->extensions = array_merge($this->extensions, $extensions);
    }

    /**
     * Register an array of custom implicit validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addImplicitExtensions(array $extensions)
    {
        $this->addExtensions($extensions);

        foreach ($extensions as $rule => $extension) {
            $this->implicitRules[] = Str::studly($rule);
        }
    }

    /**
     * Register an array of custom dependent validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addDependentExtensions(array $extensions)
    {
        $this->addExtensions($extensions);

        foreach ($extensions as $rule => $extension) {
            $this->dependentRules[] = Str::studly($rule);
        }
    }

    /**
     * Register a custom validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @return void
     */
    public function addExtension($rule, $extension)
    {
        $this->extensions[Str::snake($rule)] = $extension;
    }

    /**
     * Register a custom implicit validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @return void
     */
    public function addImplicitExtension($rule, $extension)
    {
        $this->addExtension($rule, $extension);

        $this->implicitRules[] = Str::studly($rule);
    }

    /**
     * Register a custom dependent validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @return void
     */
    public function addDependentExtension($rule, $extension)
    {
        $this->addExtension($rule, $extension);

        $this->dependentRules[] = Str::studly($rule);
    }

    /**
     * Register an array of custom validator message replacers.
     *
     * @param  array  $replacers
     * @return void
     */
    public function addReplacers(array $replacers)
    {
        if ($replacers) {
            $keys = array_map(Str::snake(...), array_keys($replacers));

            $replacers = array_combine($keys, array_values($replacers));
        }

        $this->replacers = array_merge($this->replacers, $replacers);
    }

    /**
     * Register a custom validator message replacer.
     *
     * @param  string  $rule
     * @param  \Closure|string  $replacer
     * @return void
     */
    public function addReplacer($rule, $replacer)
    {
        $this->replacers[Str::snake($rule)] = $replacer;
    }

    /**
     * Set the custom messages for the validator.
     *
     * @param  array  $messages
     * @return $this
     */
    public function setCustomMessages(array $messages)
    {
        $this->customMessages = array_merge($this->customMessages, $messages);

        return $this;
    }

    /**
     * Set the custom attributes on the validator.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function setAttributeNames(array $attributes)
    {
        $this->customAttributes = $attributes;

        return $this;
    }

    /**
     * Add custom attributes to the validator.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function addCustomAttributes(array $attributes)
    {
        $this->customAttributes = array_merge($this->customAttributes, $attributes);

        return $this;
    }

    /**
     * Set the callback that used to format an implicit attribute.
     *
     * @param  callable|null  $formatter
     * @return $this
     */
    public function setImplicitAttributesFormatter(?callable $formatter = null)
    {
        $this->implicitAttributesFormatter = $formatter;

        return $this;
    }

    /**
     * Set the custom values on the validator.
     *
     * @param  array  $values
     * @return $this
     */
    public function setValueNames(array $values)
    {
        $this->customValues = $values;

        return $this;
    }

    /**
     * Add the custom values for the validator.
     *
     * @param  array  $customValues
     * @return $this
     */
    public function addCustomValues(array $customValues)
    {
        $this->customValues = array_merge($this->customValues, $customValues);

        return $this;
    }

    /**
     * Set the fallback messages for the validator.
     *
     * @param  array  $messages
     * @return void
     */
    public function setFallbackMessages(array $messages)
    {
        $this->fallbackMessages = $messages;
    }

    /**
     * Get the Presence Verifier implementation.
     *
     * @param  string|null  $connection
     * @return \Illuminate\Validation\PresenceVerifierInterface
     *
     * @throws \RuntimeException
     */
    public function getPresenceVerifier($connection = null)
    {
        if (! isset($this->presenceVerifier)) {
            throw new RuntimeException('Presence verifier has not been set.');
        }

        if ($this->presenceVerifier instanceof DatabasePresenceVerifierInterface) {
            $this->presenceVerifier->setConnection($connection);
        }

        return $this->presenceVerifier;
    }

    /**
     * Set the Presence Verifier implementation.
     *
     * @param  \Illuminate\Validation\PresenceVerifierInterface  $presenceVerifier
     * @return void
     */
    public function setPresenceVerifier(PresenceVerifierInterface $presenceVerifier)
    {
        $this->presenceVerifier = $presenceVerifier;
    }

    /**
     * Get the exception to throw upon failed validation.
     *
     * @return class-string<\Illuminate\Validation\ValidationException>
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Set the exception to throw upon failed validation.
     *
     * @param  class-string<\Illuminate\Validation\ValidationException>  $exception
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setException($exception)
    {
        if (! is_a($exception, ValidationException::class, true)) {
            throw new InvalidArgumentException(
                sprintf('Exception [%s] is invalid. It must extend [%s].', $exception, ValidationException::class)
            );
        }

        $this->exception = $exception;

        return $this;
    }

    /**
     * Ensure exponents are within range using the given callback.
     *
     * @param  callable(int $scale, string $attribute, mixed $value)  $callback
     * @return $this
     */
    public function ensureExponentWithinAllowedRangeUsing($callback)
    {
        $this->ensureExponentWithinAllowedRangeUsing = $callback;

        return $this;
    }

    /**
     * Get the Translator implementation.
     *
     * @return \Illuminate\Contracts\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Set the Translator implementation.
     *
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @return void
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Call a custom validator extension.
     *
     * @param  string  $rule
     * @param  array  $parameters
     * @return bool|null
     */
    protected function callExtension($rule, $parameters)
    {
        $callback = $this->extensions[$rule];

        if (is_callable($callback)) {
            return $callback(...array_values($parameters));
        } elseif (is_string($callback)) {
            return $this->callClassBasedExtension($callback, $parameters);
        }
    }

    /**
     * Call a class based validator extension.
     *
     * @param  string  $callback
     * @param  array  $parameters
     * @return bool
     */
    protected function callClassBasedExtension($callback, $parameters)
    {
        [$class, $method] = Str::parseCallback($callback, 'validate');

        return $this->container->make($class)->{$method}(...array_values($parameters));
    }

    /**
     * Encode the attribute with the placeholder hash.
     *
     * @param  string  $attribute
     * @return string
     */
    protected static function encodeAttributeWithPlaceholder(string $attribute)
    {
        return str_replace('\.', '__dot__'.static::$placeholderHash, $attribute);
    }

    /**
     * Decode an attribute with a placeholder hash.
     *
     * @param  string  $attribute
     * @return string
     */
    protected static function decodeAttributeWithPlaceholder(string $attribute)
    {
        return str_replace('__dot__'.static::$placeholderHash, '\\.', $attribute);
    }

    /**
     * Flush the validator's global state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$placeholderHash = null;
    }

    /**
     * Handle dynamic calls to class methods.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $rule = Str::snake(substr($method, 8));

        if (isset($this->extensions[$rule])) {
            return $this->callExtension($rule, $parameters);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
