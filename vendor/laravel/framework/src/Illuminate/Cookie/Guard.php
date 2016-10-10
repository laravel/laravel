<?php namespace Illuminate\Cookie;

use Illuminate\Encryption\Encrypter;
use Illuminate\Encryption\DecryptException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Guard implements HttpKernelInterface {

	/**
	 * The wrapped kernel implementation.
	 *
	 * @var \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	protected $app;

	/**
	 * The encrypter instance.
	 *
	 * @var \Illuminate\Encryption\Encrypter
	 */
	protected $encrypter;

	/**
	 * Create a new CookieGuard instance.
	 *
	 * @param  \Symfony\Component\HttpKernel\HttpKernelInterface  $app
	 * @param  \Illuminate\Encryption\Encrypter  $encrypter
	 * @return void
	 */
	public function __construct(HttpKernelInterface $app, Encrypter $encrypter)
	{
		$this->app = $app;
		$this->encrypter = $encrypter;
	}

	/**
	 * Handle the given request and get the response.
	 *
	 * @implements HttpKernelInterface::handle
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  int   $type
	 * @param  bool  $catch
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		return $this->encrypt($this->app->handle($this->decrypt($request), $type, $catch));
	}

	/**
	 * Decrypt the cookies on the request.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	protected function decrypt(Request $request)
	{
		foreach ($request->cookies as $key => $c)
		{
			try
			{
				$request->cookies->set($key, $this->decryptCookie($c));
			}
			catch (DecryptException $e)
			{
				$request->cookies->set($key, null);
			}
		}

		return $request;
	}

	/**
	 * Decrypt the given cookie and return the value.
	 *
	 * @param  string|array  $cookie
	 * @return string|array
	 */
	protected function decryptCookie($cookie)
	{
		return is_array($cookie)
						? $this->decryptArray($cookie)
						: $this->encrypter->decrypt($cookie);
	}

	/**
	 * Decrypt an array based cookie.
	 *
	 * @param  array  $cookie
	 * @return array
	 */
	protected function decryptArray(array $cookie)
	{
		$decrypted = array();

		foreach ($cookie as $key => $value)
		{
			$decrypted[$key] = $this->encrypter->decrypt($value);
		}

		return $decrypted;
	}

	/**
	 * Encrypt the cookies on an outgoing response.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Response  $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function encrypt(Response $response)
	{
		foreach ($response->headers->getCookies() as $key => $c)
		{
			$encrypted = $this->encrypter->encrypt($c->getValue());

			$response->headers->setCookie($this->duplicate($c, $encrypted));
		}

		return $response;
	}

	/**
	 * Duplicate a cookie with a new value.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Cookie  $cookie
	 * @param  mixed  $value
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	protected function duplicate(Cookie $c, $value)
	{
		return new Cookie(
			$c->getName(), $value, $c->getExpiresTime(), $c->getPath(),
			$c->getDomain(), $c->isSecure(), $c->isHttpOnly()
		);
	}

}
