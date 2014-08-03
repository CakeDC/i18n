Quick Start
===========

Changing the Application language based on the URL
--------------------------------------------------

Please not that this plugin is for automatically setting the language for page contents, not for translating the URL itself!

With a few configuration changes you will be able to start internationalizing your application based on the URL. For example you might already have an application that runs in a URL like

```
http://example.com/articles/
```

And in addition you would like to show the interface in another language, in another URL looking like this one:

```
http://example.com/fr/articles/
```

To achieve this purpose you need to use the I18nRoute class that is provided in this plugin. First, download this repo or clone in your app/plugins folder  with the name i18n. Secondly open your `app/config/bootstrap.php` and add the following content:

```php
// The 3 letters code for your default language
define('DEFAULT_LANGUAGE', 'eng');

//List of languages you want to support
Configure::write('Config.languages', array('eng', 'deu', 'fre', 'jpn', 'spa', 'rus'));

// Load the plugin
CakePlugin::load('I18n', array(
	'routes' => true
));
```

Now let's start using the new Route class to support the new internationalized urls. Open your `app/Config/routes.php` and make it look like this:

```php
App::uses('I18nRoute', 'I18n.Routing/Route');
Router::connect('/',
	array('controller' => 'pages', 'action' => 'display', 'home'),
	array('routeClass' => 'I18nRoute')
);
Router::connect('/pages/*',
	array('controller' => 'pages', 'action' => 'display'),
	array('routeClass' => 'I18nRoute')
);
```

Make sure your app/Config/routes.php file contains this line:

```php
CakePlugin::routes();
```

Remove the last line of your ```app/Config/routes.php``` file, the one that looks like:

```php
require CAKE . 'Config' . DS . 'routes.php';
```

Basically every route your define in your file needs to use the I18nRoute class, so if you have custom routes defined in your routes.php your also need to change them if you want the to be I18n capable:

```php
Router::connect('/my/custo/route',
	array(/* some params */),
	array('routeClass' => 'I18nRoute, /* some params */)
);
```

Finally you need to tell CakePHP to produce the correct URL in your views. Open your `app/View/Helper/AppHelper.php` file or create it if it is not already there, and add the following content:

```php
class AppHelper extends Helper {

	public function url($url = null, $full = false) {
		if (is_array($url) && !array_key_exists('lang', $url)) {
			$url['lang'] = Configure::read('Config.language');
		}
		return parent::url($url, $full);
	}
}
```

You can tweak those line to your taste, what it is doing is just adding a named parameter to the url called `lang` filled with the current language. If you need to add exceptions for it, fell free to add more conditions to this method.

Creating a Language Switcher Widget with Flags
----------------------------------------------

If you run into the need of crating a language switcher (links to the same page but with the language changed) We have bundled a helper in this plugin for this purpose. Just add it to your controller, or AppController and use it in your views:

```php
	class AppController extends Controller {
		public $helpers = array('I18n.I18n', /* ... */);
	}
```

In the view:

```php
echo $this->I18n->flagSwitcher(array(
	'class' => 'languages',
	'id' => 'language-switcher'
));
```

The helper will look into the configure languages array and will create a list of links with flags for changing the current URL!
