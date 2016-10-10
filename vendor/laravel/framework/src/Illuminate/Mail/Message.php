<?php namespace Illuminate\Mail;

use Swift_Image;
use Swift_Attachment;

class Message {

	/**
	 * The Swift Message instance.
	 *
	 * @var \Swift_Message
	 */
	protected $swift;

	/**
	 * Create a new message instance.
	 *
	 * @param  \Swift_Message  $swift
	 * @return void
	 */
	public function __construct($swift)
	{
		$this->swift = $swift;
	}

	/**
	 * Add a "from" address to the message.
	 *
	 * @param  string  $address
	 * @param  string  $name
	 * @return \Illuminate\Mail\Message
	 */
	public function from($address, $name = null)
	{
		$this->swift->setFrom($address, $name);

		return $this;
	}

	/**
	 * Set the "sender" of the message.
	 *
	 * @param  string  $address
	 * @param  string  $name
	 * @return \Illuminate\Mail\Message
	 */
	public function sender($address, $name = null)
	{
		$this->swift->setSender($address, $name);

		return $this;
	}

	/**
	 * Set the "return path" of the message.
	 *
	 * @param  string  $address
	 * @return \Illuminate\Mail\Message
	 */
	public function returnPath($address)
	{
		$this->swift->setReturnPath($address);

		return $this;
	}

	/**
	 * Add a recipient to the message.
	 *
	 * @param  string|array  $address
	 * @param  string  $name
	 * @return \Illuminate\Mail\Message
	 */
	public function to($address, $name = null)
	{
		return $this->addAddresses($address, $name, 'To');
	}

	/**
	 * Add a carbon copy to the message.
	 *
	 * @param  string  $address
	 * @param  string  $name
	 * @return \Illuminate\Mail\Message
	 */
	public function cc($address, $name = null)
	{
		return $this->addAddresses($address, $name, 'Cc');
	}

	/**
	 * Add a blind carbon copy to the message.
	 *
	 * @param  string  $address
	 * @param  string  $name
	 * @return \Illuminate\Mail\Message
	 */
	public function bcc($address, $name = null)
	{
		return $this->addAddresses($address, $name, 'Bcc');
	}

	/**
	 * Add a reply to address to the message.
	 *
	 * @param  string  $address
	 * @param  string  $name
	 * @return \Illuminate\Mail\Message
	 */
	public function replyTo($address, $name = null)
	{
		return $this->addAddresses($address, $name, 'ReplyTo');
	}

	/**
	 * Add a recipient to the message.
	 *
	 * @param  string|array  $address
	 * @param  string  $name
	 * @param  string  $type
	 * @return \Illuminate\Mail\Message
	 */
	protected function addAddresses($address, $name, $type)
	{
		if (is_array($address))
		{
			$this->swift->{"set{$type}"}($address, $name);
		}
		else
		{
			$this->swift->{"add{$type}"}($address, $name);
		}

		return $this;
	}

	/**
	 * Set the subject of the message.
	 *
	 * @param  string  $subject
	 * @return \Illuminate\Mail\Message
	 */
	public function subject($subject)
	{
		$this->swift->setSubject($subject);

		return $this;
	}

	/**
	 * Set the message priority level.
	 *
	 * @param  int  $level
	 * @return \Illuminate\Mail\Message
	 */
	public function priority($level)
	{
		$this->swift->setPriority($level);

		return $this;
	}

	/**
	 * Attach a file to the message.
	 *
	 * @param  string  $file
	 * @param  array   $options
	 * @return \Illuminate\Mail\Message
	 */
	public function attach($file, array $options = array())
	{
		$attachment = $this->createAttachmentFromPath($file);

		return $this->prepAttachment($attachment, $options);
	}

	/**
	 * Create a Swift Attachment instance.
	 *
	 * @param  string  $file
	 * @return \Swift_Attachment
	 */
	protected function createAttachmentFromPath($file)
	{
		return Swift_Attachment::fromPath($file);
	}

	/**
	 * Attach in-memory data as an attachment.
	 *
	 * @param  string  $data
	 * @param  string  $name
	 * @param  array   $options
	 * @return \Illuminate\Mail\Message
	 */
	public function attachData($data, $name, array $options = array())
	{
		$attachment = $this->createAttachmentFromData($data, $name);

		return $this->prepAttachment($attachment, $options);
	}

	/**
	 * Create a Swift Attachment instance from data.
	 *
	 * @param  string  $data
	 * @param  string  $name
	 * @return \Swift_Attachment
	 */
	protected function createAttachmentFromData($data, $name)
	{
		return Swift_Attachment::newInstance($data, $name);
	}

	/**
	 * Embed a file in the message and get the CID.
	 *
	 * @param  string  $file
	 * @return string
	 */
	public function embed($file)
	{
		return $this->swift->embed(Swift_Image::fromPath($file));
	}

	/**
	 * Embed in-memory data in the message and get the CID.
	 *
	 * @param  string  $data
	 * @param  string  $name
	 * @param  string  $contentType
	 * @return string
	 */
	public function embedData($data, $name, $contentType = null)
	{
		$image = Swift_Image::newInstance($data, $name, $contentType);

		return $this->swift->embed($image);
	}

	/**
	 * Prepare and attach the given attachment.
	 *
	 * @param  \Swift_Attachment  $attachment
	 * @param  array  $options
	 * @return \Illuminate\Mail\Message
	 */
	protected function prepAttachment($attachment, $options = array())
	{
		// First we will check for a MIME type on the message, which instructs the
		// mail client on what type of attachment the file is so that it may be
		// downloaded correctly by the user. The MIME option is not required.
		if (isset($options['mime']))
		{
			$attachment->setContentType($options['mime']);
		}

		// If an alternative name was given as an option, we will set that on this
		// attachment so that it will be downloaded with the desired names from
		// the developer, otherwise the default file names will get assigned.
		if (isset($options['as']))
		{
			$attachment->setFilename($options['as']);
		}

		$this->swift->attach($attachment);

		return $this;
	}

	/**
	 * Get the underlying Swift Message instance.
	 *
	 * @return \Swift_Message
	 */
	public function getSwiftMessage()
	{
		return $this->swift;
	}

	/**
	 * Dynamically pass missing methods to the Swift instance.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$callable = array($this->swift, $method);

		return call_user_func_array($callable, $parameters);
	}

}
