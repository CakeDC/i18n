# Internationalization plugin for CakePHP #

Version 1.1

This plugin provides the required classes and method to be able to internationalize your application more easily. Basically it offers a very simple way of switching your application default language based on the url, It also provides classes to auto translate content using the Google translate API.

## Changing the Application language based on the url ##

With a few configuration changes you will be able to start internationalizing your application based on the url. For example you might already have an application that runs in a url like

	http://example.com/articles/
	
And in addition you would like to show the interface in another language, in another url looking like this one:

	http://example.com/fr/articles/

To achieve this purpose you need to use the I18nRoute class that is provided in this plugin. First, download this repo or clone in your app/plugins folder  with the name i18n. Secondly open your app/config/bootstrap.php and add the following content:

	define('DEFAULT_LANGUAGE', 'eng'); // The 3 letters code for your default language
	Configure::write('Config.languages', array('deu', 'fre', 'jpn', 'spa', 'rus')); //List of languages you want to support
	

Now let's start using the new Route class to support the new internationalized urls. Open your app/config/routes.php and make it look like this:

	App::import('Lib', 'I18n.I18nRoute');
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'), array('routeClass' => 'I18nRoute'));
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'I18nRoute'));

	//These 2 lines need to go at the bottom in order to make the plugin work properly
	I18nRoute::connectDefaultRoutes(array('excluded_plugin', 'another_plugin_excluded')); // Connects routes and excludes some plugin routes if you need to
	I18nRoute::promoteLangRoutes(); // This reorganizes the route definitions internally so the default language takes priority

Basically every route your define in your file needs to use the I18nRoute class, so if you have custom routes defined in your routes.php your also need to change them if you want the to be I18n capable:

	Router::connect('/my/custo/route', array(... some params ...), array('routeClass' => 'I18nRoute, ... more params ...));

Finally you need to tell CakePHP to produce the correct url in your views. Open your app/app\_helper.php file or create it if it is not already there, and add the following content:

	class AppHelper extends Helper {

		public function url($url = null, $full = false) {
			if (empty(is_array($url) && !array_key_exists('lang', $url)) {
				$url['lang'] = Configure::read('Config.language');
			}
			return parent::url($url, $full);
		}
	}

You can tweak those line to your taste, what it is doing is just adding a named parameter to the url called 'lang' filled with the current language. If you need to add exceptions for it, fell free to add more conditions to this method.


## Creating a language switcher widget with flags ##

If you run into the need of crating a language switcher (links to the same page but with the language changed) We have bundled a helper in this plugin for this purpose. Just add it to your controller, or AppController and use it in your views:

	class AppController extends Controller {
		public $helpers = array('I18n.I18n', ...);
	}

	//a_view.ctp
	<?php echo $this->I18n->flagSwitcher(array('class' => 'languages', 'id' => 'language-switcher')); ?>

The helper will look into the configure languages array and will create a list of links with flags for changing the current url!

## Requirements ##

* PHP version: PHP 5.2+
* CakePHP version: Cakephp 1.3 Stable

## Support ##

For support and feature request, please visit the [I18n Plugin Support Site](http://cakedc.lighthouseapp.com/projects/59613-i18n-plugin/).

For more information about our Professional CakePHP Services please visit the [Cake Development Corporation website](http://cakedc.com).

## License ##

Copyright 2009-2010, [Cake Development Corporation](http://cakedc.com)

Licensed under [The MIT License](http://www.opensource.org/licenses/mit-license.php)<br/>
Redistributions of files must retain the above copyright notice.

## Copyright ###

Copyright 2009-2011<br/>
[Cake Development Corporation](http://cakedc.com)<br/>
1785 E. Sahara Avenue, Suite 490-423<br/>
Las Vegas, Nevada 89104<br/>
http://cakedc.com<br/>