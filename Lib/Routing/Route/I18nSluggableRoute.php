<?php
/**
 * Copyright 2009-2011, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::import('Lib', array('I18n.I18nRoute', 'Slugger.SluggableRoute'));
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
		$this->_Sluggable = new SluggableRoute($template, $defaults, $options);
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
		if (!isset($this->options['models'])) {
			return false;
		}
		
		if (isset($this->options['models'])) {
			$i = -1;
			foreach ($this->options['models'] as $checkNamed => $slugField) {
				$i++;
				if (is_numeric($checkNamed)) {
					$checkNamed = $slugField;
					$slugField = null;
				}
				if (isset($url[$i])) {
					$slugSet = $this->_Sluggable->getSlugs($checkNamed, $slugField);
					if (empty($slugSet)) {
						return false;
					}
					if (!isset($slugSet[$url[$i]])) {
						return false;
					}
					$url[$checkNamed] = $slugSet[$url[$i]];
					unset($url[$i]);
				}
			}
		}
		return parent::match($url);
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
		if (empty($params)) {
			return false;
		}

		if (isset($this->options['models'])) {
			$i = -1;
			$passed = array();
			foreach ($this->options['models'] as $checkNamed => $slugField) {
				$i++;
				if (is_numeric($checkNamed)) {
					$checkNamed = $slugField;
					$slugField = null;
				}
				$passed[$i] = null;
				if (!isset($params[$checkNamed])) {
					return false;
				}
				$slug = $params[$checkNamed];
				$slugSet = $this->_Sluggable->getSlugs($checkNamed, $slugField);
				if (empty($slugSet)) {
					return false;
				}
				$slugSet = array_flip($slugSet);
				if (!isset($slugSet[$slug])) {
					return false;
				}
				$passed[$i] = $slugSet[$slug];
			}
			$params['pass'] = array_merge($passed, isset($params['pass']) ? $params['pass'] : array());
			return $params;
		}
		return false;
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