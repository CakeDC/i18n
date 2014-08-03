<?php
/**
 * Copyright 2009-2011, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2014, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('I18nRoute', 'I18n.Routing/Route');
App::uses('SluggableRoute', 'I18n.Routing/Route');

/**
 * I18nSluggable Route
 *
 * Code mainly duplicated from Jeremy Harris SluggableRoute
 * @see http://codaset.com/jeremyharris/slugger
 */
class I18nSluggableRoute extends I18nRoute {

/**
 * Class name - Workaround to be able to extend this class without breaking existing features
 *
 * @var string
 */
	public $name = __CLASS__;

/**
 * Instance of the Sluggabble route
 * Used only for its internal methods
 * 
 * @var SluggableRoute
 */
	protected $_Sluggable = null;

/**
 * Constructor for a Route
 *
 * @param string $template Template string with parameter placeholders
 * @param array $defaults Array of defaults for the route.
 * @param array $options Array of parameters and additional options for the Route
 * @return void
 */
	public function __construct($template, $defaults = array(), $options = array()) {
		parent::__construct($template, $defaults, $options);
		$this->_Sluggable = new SluggableRoute($this->template, $defaults, $options);
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
		return $this->_Sluggable->match($url);
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
		$params = $this->_Sluggable->parse($url);
		if ($params !== false && array_key_exists('lang', $params)) {
			Configure::write('Config.language', $params['lang']);
		}
		return $params;
	}

/**
 * Invalidate cached slugs for a given model or entry
 *
 * @param string $modelName Name of the model to invalidate cache for
 * @param string $id If of the only entry to update
 * @return boolean True if the value was succesfully deleted, false if it didn't exist or couldn't be removed
 * @access public
 */
	public static function invalidateCache($modelName, $id = null) {
		return SluggableRoute::invalidateCache($modelName, $id);
	}

}