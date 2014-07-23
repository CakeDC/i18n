<?php
/**
 * Copyright 2009-2014, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2014, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeRoute', 'Routing/Route');

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
 * @param array  $defaults Array of defaults for the route.
 * @param array  $options Array of parameters and additional options for the Route
 * @return \I18nRoute
 */
	public function __construct($template, $defaults = array(), $options = array()) {
		if (strpos($template, ':lang') === false && empty($options['disableAutoNamedLang'])) {
			Router::connect(
				$template,
				$defaults + array('lang' => DEFAULT_LANGUAGE),
				array('disableAutoNamedLang' => true, 'routeClass' => $this->name) + $options
			);
			$options += array('__promote' => true);
			$template = '/:lang' . $template;
		}

		$options = array_merge((array)$options, array(
			'lang' => join('|', Configure::read('Config.languages')),
		));
		unset($options['disableAutoNamedLang']);

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
			$url['lang'] = $this->getDefaultLanguage();
		}
		$parentMatch = parent::match($url);
		if (!$parentMatch) {
			return false;
		}

		if ($this->_shouldStripDefaultLanguageOnMatch()) {
			$parentMatch = preg_replace('#/' . DEFAULT_LANGUAGE . '/#', '/', $parentMatch);
		}
		return $parentMatch;
	}

/**
 * Checks to see if the given URL can be parsed by this route.
 * If the route can be parsed an array of parameters will be returned if not
 * false will be returned. String urls are parsed if they match a routes regular expression.
 *
 * @param string $url The url to attempt to parse.
 * @return mixed Boolean false on failure, otherwise an array or parameters
 * @access public
 */
	public function parse($url) {
		$params = parent::parse($url);
		if (!empty($params['named']['lang'])) {
			$params['lang'] = $params['named']['lang'];
			unset($params['named']['lang']);
		}
		if ($params !== false && array_key_exists('lang', $params)) {
			$params['lang'] = empty($params['lang']) ? DEFAULT_LANGUAGE : $params['lang'];
			Configure::write('Config.language', $params['lang']);
		}
		return $params;
	}

/**
 * Whether the default language code should be removed from the matched url
 *
 * @return boolean
 */
	protected function _shouldStripDefaultLanguageOnMatch() {
		$hasNamedParam = strpos($this->template, ':lang') !== false;
		$hasHardcodedDefaultLang = strpos($this->template, '/' . DEFAULT_LANGUAGE . '/') !== false;
		return !($hasNamedParam || $hasHardcodedDefaultLang);
	}

/**
 * Return default language
 *
 * @return string
 */
	public function getDefaultLanguage() {
		$lang = Configure::read('Config.languageOverride');
		if (empty($lang)) {
			$lang = Configure::read('Config.language');
			return $lang;
		}
		return $lang;
	}

}