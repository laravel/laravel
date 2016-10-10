<?php namespace Illuminate\Http;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\Contracts\MessageProviderInterface;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse {

	/**
	 * The request instance.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * The session store implementation.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Set a header on the Response.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @param  bool  $replace
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function header($key, $value, $replace = true)
	{
		$this->headers->set($key, $value, $replace);

		return $this;
	}

	/**
	 * Flash a piece of data to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function with($key, $value = null)
	{
		if (is_array($key))
		{
			foreach ($key as $k => $v) $this->with($k, $v);
		}
		else
		{
			$this->session->flash($key, $value);
		}

		return $this;
	}

	/**
	 * Add a cookie to the response.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Cookie  $cookie
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function withCookie(Cookie $cookie)
	{
		$this->headers->setCookie($cookie);

		return $this;
	}

	/**
	 * Flash an array of input to the session.
	 *
	 * @param  array  $input
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function withInput(array $input = null)
	{
		$input = $input ?: $this->request->input();

		$this->session->flashInput($input);

		return $this;
	}

	/**
	 * Flash an array of input to the session.
	 *
	 * @param  dynamic  string
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function onlyInput()
	{
		return $this->withInput($this->request->only(func_get_args()));
	}

	/**
	 * Flash an array of input to the session.
	 *
	 * @param  dynamic  string
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function exceptInput()
	{
		return $this->withInput($this->request->except(func_get_args()));
	}

	/**
	 * Flash a container of errors to the session.
	 *
	 * @param  \Illuminate\Support\Contracts\MessageProviderInterface|array  $provider
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function withErrors($provider)
	{
		if ($provider instanceof MessageProviderInterface)
		{
			$this->with('errors', $provider->getMessageBag());
		}
		else
		{
			$this->with('errors', new MessageBag((array) $provider));
		}

		return $this;
	}

	/**
	 * Get the request instance.
	 *
	 * @return  \Illuminate\Http\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Set the request instance.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Get the session store implementation.
	 *
	 * @return \Illuminate\Session\Store
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Set the session store implementation.
	 *
	 * @param  \Illuminate\Session\Store  $store
	 * @return void
	 */
	public function setSession(SessionStore $session)
	{
		$this->session = $session;
	}

	/**
	 * Dynamically bind flash data in the session.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return void
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (starts_with($method, 'with'))
		{
			return $this->with(snake_case(substr($method, 4)), $parameters[0]);
		}

		throw new \BadMethodCallException("Method [$method] does not exist on Redirect.");
	}

}
