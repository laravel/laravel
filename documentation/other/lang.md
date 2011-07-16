## Localization

- [The Basics](#basics)
- [Retrieving A Language Line](#get)
- [Place Holders & Replacements](#replace)

<a name="basics"></a>
### The Basics

Localization is the process of translating your application into different languages. The **Lang** class provides a simple mechanism to help you organize and retrieve the text of your multilingual application.

All of the language files for your application live under the **application/lang** directory. Within the **application/lang** directory, you should create a directory for each language your application speaks. So, for example, if your application speaks English and Spanish, you might create **en** and **sp** directories under the **lang** directory.

Each language directory may contain many different language files. Each language file is simply an array of string values in that language. In fact, language files are structured identically to configuration files. For example, within the **application/lang/en** directory, you could create a **marketing.php** file that looks like this:

	return array(

	     'welcome' => 'Welcome to our website!',

	);

Next, you should create a corresponding **marketing.php** file within the **application/lang/sp** directory. The file would look something like this:

	return array(

	     'welcome' => 'Bienvenido a nuestro sitio web!',

	);

Nice! Now you know how to get started setting up your language files and directories. Let's keep localizing!

<a name="basics"></a>
### Retrieving A Language Line

To retrieve a language line, first create a Lang instance using the **line** method, then call the **get** method on the instance:

	echo Lang::line('marketing.welcome')->get();

Notice how a dot was used to separate "marketing" and "welcome"? The text before the dot corresponds to the language file, while the text after the dot corresponds to a specific string within that file.

But, how did the method know which language directory to retrieve the message from? By default, the **get** method will use the language specified in your **application/config/application.php** configuration file. In this file you may set the default language of your application using the **language** option:

	'language' => 'en'

Need to retrieve the line in a language other than your default? Not a problem. Just mention the language to the **get** method:

	echo Lang::line('marketing.welcome')->get('sp');

<a name="replace"></a>
### Place Holders & Replacements

Now, let's work on our welcome message. "Welcome to our website!" is a pretty generic message. It would be helpful to be able to specify the name of the person we are welcoming. But, creating a language line for each user of our application would be time-consuming and ridiculous. Thankfully, you don't have to. You can specify "place-holders" within your language lines. Place-holders are preceeded by a colon:

	'welcome' => 'Welcome to our website, :name!'

Then, simply pass an array of place-holder replacements to the **replace** method on a Lang instance:

	echo Lang::line('marketing.welcome')->replace(array('name' => 'Taylor'))->get();

This statement will return a nice, heart-warming welcome message:

	Welcome to our website, Taylor!