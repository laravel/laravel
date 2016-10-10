<?php namespace Illuminate\Foundation\Testing;

use Illuminate\Foundation\Application;
use Symfony\Component\HttpKernel\Client as BaseClient;
use Symfony\Component\BrowserKit\Request as DomRequest;

class Client extends BaseClient {

	/**
	 * Convert a BrowserKit request into a Illuminate request.
	 *
	 * @param  \Symfony\Component\BrowserKit\Request  $request
	 * @return \Illuminate\Http\Request
	 */
	protected function filterRequest(DomRequest $request)
	{
		$httpRequest = Application::onRequest('create', $this->getRequestParameters($request));

		$httpRequest->files->replace($this->filterFiles($httpRequest->files->all()));

		return $httpRequest;
	}

	/**
	 * Get the request parameters from a BrowserKit request.
	 *
	 * @param  \Symfony\Component\BrowserKit\Request  $request
	 * @return array
	 */
	protected function getRequestParameters(DomRequest $request)
	{
		return array(
			$request->getUri(), $request->getMethod(), $request->getParameters(), $request->getCookies(),
			$request->getFiles(), $request->getServer(), $request->getContent()
		);
	}

}
