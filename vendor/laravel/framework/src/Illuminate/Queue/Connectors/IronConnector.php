<?php namespace Illuminate\Queue\Connectors;

use IronMQ;
use Illuminate\Http\Request;
use Illuminate\Queue\IronQueue;
use Illuminate\Encryption\Encrypter;

class IronConnector implements ConnectorInterface {

	/**
	 * The encrypter instance.
	 *
	 * @var \Illuminate\Encryption\Encrypter
	 */
	protected $crypt;

	/**
	 * The current request instance.
	 *
	 * @var \Illuminate\Http\Request;
	 */
	protected $request;

	/**
	 * Create a new Iron connector instance.
	 *
	 * @param  \Illuminate\Encryption\Encrypter  $crypt
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	public function __construct(Encrypter $crypt, Request $request)
	{
		$this->crypt = $crypt;
		$this->request = $request;
	}

	/**
	 * Establish a queue connection.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Queue\QueueInterface
	 */
	public function connect(array $config)
	{
		$ironConfig = array('token' => $config['token'], 'project_id' => $config['project']);

		if (isset($config['host'])) $ironConfig['host'] = $config['host'];

		$iron = new IronMQ($ironConfig);

		if (isset($config['ssl_verifypeer']))
		{
			$iron->ssl_verifypeer = $config['ssl_verifypeer'];
		}

		return new IronQueue($iron, $this->crypt, $this->request, $config['queue']);
	}

}
