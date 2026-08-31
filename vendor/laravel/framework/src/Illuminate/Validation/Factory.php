<?php

namespace Illuminate\Validation;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Support\Str;

class Factory implements FactoryContract
{
    /**
     * The Translator implementation.
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * The Presence Verifier implementation.
     *
     * @var \Illuminate\Validation\PresenceVerifierInterface
     */
    protected $verifier;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container|null
     */
    protected $container;

    /**
     * All of the custom validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $extensions = [];

    /**
     * All of the custom implicit validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $implicitExtensions = [];

    /**
     * All of the custom dependent validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $dependentExtensions = [];

    /**
     * All of the custom validator message replacers.
     *
     * @var array<string, \Closure|string>
     */
    protected $replacers = [];

    /**
     * All of the fallback messages for custom rules.
     *
     * @var array<string, string>
     */
    protected $fallbackMessages = [];

    /**
     * Indicates that unvalidated array keys should be excluded, even if the parent array was validated.
     *
     * @var bool
     */
    protected $excludeUnvalidatedArrayKeys = true;

    /**
     * The Validator resolver instance.
     *
     * @var \Closure
     */
    protected $resolver;

    /**
     * Create a new Validator factory instance.
     *
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     */
    public function __construct(Translator $translator, ?Container $container = null)
    {
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * Create a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return \Illuminate\Validation\Validator
     */
    public function make(array $data, array $rules, array $messages = [], array $attributes = [])
    {
        $validator = $this->resolve(
            $data, $rules, $messages, $attributes
        );

        // The presence verifier is responsible for checking the unique and exists data
        // for the validator. It is behind an interface so that multiple versions of
        // it may be written besides database. We'll inject it into the validator.
        if (! is_null($this->verifier)) {
            $validator->setPresenceVerifier($this->verifier);
        }

        // Next we'll set the IoC container instance of the validator, which is used to
        // resolve out class based validator extensions. If it is not set then these
        // types of extensions will not be possible on these validation instances.
        if (! is_null($this->container)) {
            $validator->setContainer($this->container);
        }

        $validator->excludeUnvalidatedArrayKeys = $this->excludeUnvalidatedArrayKeys;

        $this->addExtensions($validator);

        return $validator;
    }

    /**
     * Validate the given data against the provided rules.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data, array $rules, array $messages = [], array $attributes = [])
    {
        return $this->make($data, $rules, $messages, $attributes)->validate();
    }

    /**
     * Resolve a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return \Illuminate\Validation\Validator
     */
    protected function resolve(array $data, array $rules, array $messages, array $attributes)
    {
        if (is_null($this->resolver)) {
            return new Validator($this->translator, $data, $rules, $messages, $attributes);
        }

        return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $attributes);
    }

    /**
     * Add the extensions to a validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function addExtensions(Validator $validator)
    {
        $validator->addExtensions($this->extensions);

        // Next, we will add the implicit extensions, which are similar to the required
        // and accepted rule in that they're run even if the attributes aren't in an
        // array of data which is given to a validator instance via instantiation.
        $validator->addImplicitExtensions($this->implicitExtensions);

        $validator->addDependentExtensions($this->dependentExtensions);

        $validator->addReplacers($this->replacers);

        $validator->setFallbackMessages($this->fallbackMessages);
    }

    /**
     * Register a custom validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @param  string|null  $message
     * @return void
     */
    public function extend($rule, $extension, $message = null)
    {
        $this->extensions[$rule] = $extension;

        if ($message) {
            $this->fallbackMessages[Str::snake($rule)] = $message;
        }
    }

    /**
     * Register a custom implicit validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @param  string|null  $message
     * @return void
     */
    public function extendImplicit($rule, $extension, $message = null)
    {
        $this->implicitExtensions[$rule] = $extension;

        if ($message) {
            $this->fallbackMessages[Str::snake($rule)] = $message;
        }
    }

    /**
     * Register a custom dependent validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @param  string|null  $message
     * @return void
     */
    public function extendDependent($rule, $extension, $message = null)
    {
        $this->dependentExtensions[$rule] = $extension;

        if ($message) {
            $this->fallbackMessages[Str::snake($rule)] = $message;
        }
    }

    /**
     * Register a custom validator message replacer.
     *
     * @param  string  $rule
     * @param  \Closure|string  $replacer
     * @return void
     */
    public function replacer($rule, $replacer)
    {
        $this->replacers[$rule] = $replacer;
    }

    /**
     * Indicate that unvalidated array keys should be included in validated data when the parent array is validated.
     *
     * @return void
     */
    public function includeUnvalidatedArrayKeys()
    {
        $this->excludeUnvalidatedArrayKeys = false;
    }

    /**
     * Indicate that unvalidated array keys should be excluded from the validated data, even if the parent array was validated.
     *
     * @return void
     */
    public function excludeUnvalidatedArrayKeys()
    {
        $this->excludeUnvalidatedArrayKeys = true;
    }

    /**
     * Set the Validator instance resolver.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public function resolver(Closure $resolver)
    {
        $this->resolver = $resolver;
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
     * Get the Presence Verifier implementation.
     *
     * @return \Illuminate\Validation\PresenceVerifierInterface
     */
    public function getPresenceVerifier()
    {
        return $this->verifier;
    }

    /**
     * Set the Presence Verifier implementation.
     *
     * @param  \Illuminate\Validation\PresenceVerifierInterface  $presenceVerifier
     * @return void
     */
    public function setPresenceVerifier(PresenceVerifierInterface $presenceVerifier)
    {
        $this->verifier = $presenceVerifier;
    }

    /**
     * Get the container instance used by the validation factory.
     *
     * @return \Illuminate\Contracts\Container\Container|null
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set the container instance used by the validation factory.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
