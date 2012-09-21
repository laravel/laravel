# Localization

## Contents

- [The Basics](#the-basics)
- [Retrieving A Language Line](#get)
- [Place Holders & Replacements](#replace)

<a name="the-basics"></a>
## The Basics

Localization is the process of translating your application into different languages. The **Lang** class provides a simple mechanism to help you organize and retrieve the text of your multilingual application.

All of the language files for your application live under the **application/language** directory. Within the **application/language** directory, you should create a directory for each language your application speaks. So, for example, if your application speaks English and Spanish, you might create **en** and **sp** directories under the **language** directory.

Each language directory may contain many different language files. Each language file is simply an array of string values in that language. In fact, language files are structured identically to configuration files. For example, within the **application/language/en** directory, you could create a **marketing.php** file that looks like this:

#### Creating a language file:

	return array(

	     'welcome' => 'Welcome to our website!',

	);

Next, you should create a corresponding **marketing.php** file within the **application/language/sp** directory. The file would look something like this:

	return array(

	     'welcome' => 'Bienvenido a nuestro sitio web!',

	);

Nice! Now you know how to get started setting up your language files and directories. Let's keep localizing!

<a name="get"></a>
## Retrieving A Language Line

#### Retrieving a language line:

	echo Lang::line('marketing.welcome')->get();

#### Retrieving a language line using the "__" helper:

	echo __('marketing.welcome');

Notice how a dot was used to separate "marketing" and "welcome"? The text before the dot corresponds to the language file, while the text after the dot corresponds to a specific string within that file.

Need to retrieve the line in a language other than your default? Not a problem. Just mention the language to the **get** method:

#### Getting a language line in a given language:

	echo Lang::line('marketing.welcome')->get('sp');

<a name="replace"></a>
## Place Holders & Replacements

Now, let's work on our welcome message. "Welcome to our website!" is a pretty generic message. It would be helpful to be able to specify the name of the person we are welcoming. But, creating a language line for each user of our application would be time-consuming and ridiculous. Thankfully, you don't have to. You can specify "place-holders" within your language lines. Place-holders are preceded by a colon:

#### Creating a language line with place-holders:

	'welcome' => 'Welcome to our website, :name!'

#### Retrieving a language line with replacements:

	echo Lang::line('marketing.welcome', array('name' => 'Taylor'))->get();

#### Retrieving a language line with replacements using "__":

	echo __('marketing.welcome', array('name' => 'Taylor'));