# Profiler

## Contents
- [Enabling the Proiler](#enable)
- [Logging to the Proiler](#logging)
- [Timers and Benchmarking](#timers)

<a name="enable"></a>
## Enabling the Profiler

To enable the profiler, you need to edit **application/config/application.php** and switch the profiler option to **true**.

    'profiler' => true,

This will attach the profiler code to **all** responses coming back from your laravel install.

**Note:** As of the time of this writing a common problem with the profiler being enabled is any requests that return JSON will also include the profiler code, and destroy the JSON syntax in the response.

<a name="logging"></a>
## Logging

It is possible to use the profiler to the Log viewing portion of the profiler. Throughout your application you can call the logger and have it displayed when the profiler is rendered. 

#### Logging to the profiler:

    Profiler::log('info', 'Log some information to the profiler');

<a name="timers"></a>
## Timers and Benchmarking

Timing and benchmarking your app is simple with the ```tick()``` function on the profiler. It allows you to set various different timers in your app and will show you their performance when your app ends execution. 

Each timer can have it's own individual name which gives it a timeline. Every timer with the same name is another 'tick' on that timeline. Each timer can also execute a callback on it to perform other operations.

#### Using the generic timer timeline

    Profiler::tick();
	Profiler::tick();

#### Using multiple named timers with seperate timelines

    Profiler::tick('myTimer');
	Profiler::tick('nextTimer');
	Profiler::tick('myTimer');
	Profiler::tick('nextTimer');

#### Using a named timer with a callback
    Profiler::tick('myTimer', function($timers) {
	    echo "I'm inside the timer callback!"; 
	});
