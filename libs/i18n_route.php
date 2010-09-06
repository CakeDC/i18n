<?php
/**
 * Copyright 2009-2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * i18n Route
 *
 * @package i18n
 * @subpackage i18n.libs
 */
class I18nRoute extends CakeRoute {

/**
 * Internal flag to know whether default routes were mapped or not
 * 
 * @var boolean
 */	
	private static $__defaultsMapped = false;

/**
 * Class name - Workaround to be able to extend this class without breaking existing features
 *
 * @var string
 */
	public $name = __CLASS__;
	
/**
 * Constructor for a Route
 * Add a regex condition on the lang param to be sure it matches the available langs
 *
 * @param string $template Template string with parameter placeholders
 * @param array $defaults Array of defaults for the route.
 * @param string $params Array of parameters and additional options for the Route
 * @return void
 */
	public function __construct($template, $defaults = array(), $options = array()) {
		if (strpos($template, ':lang') === false && empty($options['disableAutoNamedLang'])) {
			$template = '/:lang' . $template;
		}
		if (strpos($template, ':lang')) {
			if (defined('DEFAULT_LANGUAGE') && empty($options['disableDefaultConnect'])) {
				// Connects the default language without the :lang param
				Router::connect(
					str_replace('/:lang', '', $template),
					array_merge($defaults, array('lang' => DEFAULT_LANGUAGE)),
					array_merge($options, array('routeClass' => $this->name, 'disableAutoNamedLang' => true)));
			}
			$options = array_merge((array)$options, array(
				'lang' => join('|', Configure::read('Config.languages')),
			));

			$Router = Router::getInstance();
			$routesList = Configure::read('I18nRoute.routes');
			$routesList[] = count($Router->routes);
			Configure::write('I18nRoute.routes', $routesList);
		}
		unset($options['disableAutoNamedLang'], $options['disableDefaultConnect']);
		
		if ($template == '/:lang/') {
			$template = '/:lang';
		}
		parent::__construct($template, $defaults, $options);
	}

/**
 * Attempt to match a url array.  If the url matches the route parameters + settings, then
 * return a generated string url.  If the url doesn't match the route parameters false will be returned.
 * This method handles the reverse routing or conversion of url arrays into string urls.
 *
 * @param array $url An array of parameters to check matching with.
 * @return mixed Either a string url for the parameters if they match or false.
 */
	public function match($url) {
		if (empty($url['lang'])) {
			$url['lang'] = Configure::read('Config.language');
		}
		return parent::match($url);
	}

/**
 * Connects the default, built-in routes, including prefix and plugin routes with the i18n custom Route
 * Code mostly duplicated from Router::__connectDefaultRoutes
 *
 * @TODO Add Short route for plugins
 * @see Router::__connectDefaultRoutes
 * @param array $pluginExceptions Plugins ommited from the lang default routing
 * @return void
 */
	public static function connectDefaultRoutes($pluginExceptions = array()) {
		if (!self::$__defaultsMapped) {
			Router::defaults(false);
			$options = array('routeClass' => __CLASS__);
			$prefixes = Router::prefixes();
			
			if ($plugins = App::objects('plugin')) {
				foreach ($plugins as $key => $value) {
					$plugins[$key] = Inflector::underscore($value);
				}
				$plugins = array_diff($plugins, $pluginExceptions);

				$pluginPattern = implode('|', $plugins);
				$match = array('plugin' => $pluginPattern) + $options;
				
				foreach ($prefixes as $prefix) {
					$params = array('prefix' => $prefix, $prefix => true);
					$indexParams = $params + array('action' => 'index');
					Router::connect("/{$prefix}/:plugin/:controller", $indexParams, $match);
					Router::connect("/{$prefix}/:plugin/:controller/:action/*", $params, $match);
				}
				Router::connect('/:plugin/:controller', array('action' => 'index'), $match);
				Router::connect('/:plugin/:controller/:action/*', array(), $match);
			}
	
			foreach ($prefixes as $prefix) {
				$params = array('prefix' => $prefix, $prefix => true);
				$indexParams = $params + array('action' => 'index');
				Router::connect("/{$prefix}/:controller/:action/*", $params, $options);
				Router::connect("/{$prefix}/:controller", $indexParams, $options);
			}
			Router::connect('/:controller', array('action' => 'index'), $options);
			Router::connect('/:controller/:action/*', array(), $options);

			$Router = Router::getInstance();
			if ($Router->named['rules'] === false) {
				$Router->connectNamed(true);
			}

			self::$__defaultsMapped = true;
		}
	}

/**
 * Promote all the lang routes before their automatically created route for the default language
 *
 * @return void
 */
	public static function promoteLangRoutes() {
		$routesList = Configure::read('I18nRoute.routes');
		if (!empty($routesList)) {
			$Router = Router::getInstance();
			$lastIndex = count($Router->routes) - 1;
			rsort($routesList);
			foreach($routesList as $langRouteIndex) {
				while ($langRouteIndex < $lastIndex) {
					Router::promote();
					$lastIndex--;
				}
				Router::promote(count($Router->routes) - 2);
				Router::promote();
				$lastIndex = $langRouteIndex - 2;
			}
			Configure::write('I18nRoute.routes', array());
		}
	}

/**
 * Reset all the internal static variables.
 * Convenience method for using in tests
 *
 * @return void
 */
	public static function reload() {
		Configure::write('I18nRoute.routes', array());
		self::$__defaultsMapped = false;
		Router::reload();
	}
}
