<?php namespace Illuminate\Validation;

use Closure;
use Illuminate\Container\Container;
use Symfony\Component\Translation\TranslatorInterface;

class Factory {

	/**
	 * The Translator implementation.
	 *
	 * @var \Symfony\Component\Translation\TranslatorInterface
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
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * All of the custom validator extensions.
	 *
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * All of the custom implicit validator extensions.
	 *
	 * @var array
	 */
	protected $implicitExtensions = array();

	/**
	 * All of the custom validator message replacers.
	 *
	 * @var array
	 */
	protected $replacers = array();

	/**
	 * All of the fallback messages for custom rules.
	 *
	 * @var array
	 */
	protected $fallbackMessages = array();

	/**
	 * The Validator resolver instance.
	 *
	 * @var Closure
	 */
	protected $resolver;

	/**
	 * Create a new Validator factory instance.
	 *
	 * @param  \Symfony\Component\Translation\TranslatorInterface  $translator
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
	public function __construct(TranslatorInterface $translator, Container $container = null)
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
	 * @param  array  $customAttributes
	 * @return \Illuminate\Validation\Validator
	 */
	public function make(array $data, array $rules, array $messages = array(), array $customAttributes = array())
	{
		// The presence verifier is responsible for checking the unique and exists data
		// for the validator. It is behind an interface so that multiple versions of
		// it may be written besides database. We'll inject it into the validator.
		$validator = $this->resolve($data, $rules, $messages, $customAttributes);

		if ( ! is_null($this->verifier))
		{
			$validator->setPresenceVerifier($this->verifier);
		}

		// Next we'll set the IoC container instance of the validator, which is used to
		// resolve out class based validator extensions. If it is not set then these
		// types of extensions will not be possible on these validation instances.
		if ( ! is_null($this->container))
		{
			$validator->setContainer($this->container);
		}

		$this->addExtensions($validator);

		return $validator;
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
		// and accepted rule in that they are run even if the attributes is not in a
		// array of data that is given to a validator instances via instantiation.
		$implicit = $this->implicitExtensions;

		$validator->addImplicitExtensions($implicit);

		$validator->addReplacers($this->replacers);

		$validator->setFallbackMessages($this->fallbackMessages);
	}

	/**
	 * Resolve a new Validator instance.
	 *
	 * @param  array  $data
	 * @param  array  $rules
	 * @param  array  $messages
	 * @param  array  $customAttributes
	 * @return \Illuminate\Validation\Validator
	 */
	protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
	{
		if (is_null($this->resolver))
		{
			return new Validator($this->translator, $data, $rules, $messages, $customAttributes);
		}
		else
		{
			return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);
		}
	}

	/**
	 * Register a custom validator extension.
	 *
	 * @param  string  $rule
	 * @param  \Closure|string  $extension
	 * @param  string  $message
	 * @return void
	 */
	public function extend($rule, $extension, $message = null)
	{
		$this->extensions[$rule] = $extension;

		if ($message) $this->fallbackMessages[snake_case($rule)] = $message;
	}

	/**
	 * Register a custom implicit validator extension.
	 *
	 * @param  string   $rule
	 * @param  \Closure|string  $extension
	 * @param  string  $message
	 * @return void
	 */
	public function extendImplicit($rule, $extension, $message = null)
	{
		$this->implicitExtensions[$rule] = $extension;

		if ($message) $this->fallbackMessages[snake_case($rule)] = $message;
	}

	/**
	 * Register a custom implicit validator message replacer.
	 *
	 * @param  string   $rule
	 * @param  \Closure|string  $replacer
	 * @return void
	 */
	public function replacer($rule, $replacer)
	{
		$this->replacers[$rule] = $replacer;
	}

	/**
	 * Set the Validator instance resolver.
	 *
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function resolver(Closure $resolver)
	{
		$this->resolver = $resolver;
	}

	/**
	 * Get the Translator implementation.
	 *
	 * @return \Symfony\Component\Translation\TranslatorInterface
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

}
