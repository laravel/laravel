# IoC Container

- [Definition](/docs/ioc#definition)
- [Registering Objects](/docs/ioc#register)
- [Resolving Objects](/docs/ioc#resolve)

<a name="definition"></a>
## Definition

An IoC container is simply a way of managing the creation of objects. You can use it to define the creation of complex objects, allowing you to resolve them throughout your application using a single line of code. You may also use it to "inject" dependencies into your classes and controllers.

IoC containers help make your application more flexible and testable. Since you may register alternate implementations of an interface with the container, you may isolate the code you are testing from external dependencies using [stubs and mocks](http://martinfowler.com/articles/mocksArentStubs.html).

<a name="register"></a>
## Registering Objects

#### Registering a resolver in the IoC container:

	IoC::register('mailer', function()
	{
		$transport = Swift_MailTransport::newInstance();

		return Swift_Mailer::newInstance($transport);
	});


Great! Now we have registered a resolver for SwiftMailer in our container. But, what if we don't want the container to create a new mailer instance every time we need one? Maybe we just want the container to return the same instance after the intial instance is created. Just tell the container the object should be a singleton:

#### Registering a singleton in the container:

	IoC::singleton('mailer', function()
	{
		//
	});

You may also register an existing object instance as a singleton in the container.

#### Registering an existing instance in the container:

	IoC::instance('mailer', $instance);

<a name="resolve"></a>
## Resolving Objects

Now that we have SwiftMailer registered in the container, we can resolve it using the **resolve** method on the **IoC** class:

	$mailer = IoC::resolve('mailer');

> **Note:** You may also [register controllers in the container](/docs/controllers#dependency-injection).