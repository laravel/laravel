## Benchmarking Code

- [The Basics](#basics)
- [Using Timers](#timers)
- [Checking Memory Usage](#memory)

<a name="basics"></a>
### The Basics

When making changes to your code, it's helpful to know the performance impact of your changes. Laravel provides a simple class to help you time code execution and check memory consumption. It's called the **Benchmark** class and it's a breeze to use.

<a name="timers"></a>
### Using Timers

To start a timer, simply call the **start** method on the Benchmark class and give your timer a name:

	Benchmark::start('foo');

Pretty easy, right?

You can easily check how much time has elapsed (in milliseconds) using the **check** method. Again, just mention the name of the timer to the method:

	echo Benchmark::check('foo');

<a name="memory"></a>
### Checking Memory Usage

Need to know how much memory is being used by your application? No problem. Just call the **memory** method to get your current memory usage in megabytes:

	echo Benchmark::memory();