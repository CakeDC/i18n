<?php
/**
 * Sluggable route class.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2010, Jeremy Harris
 * @link          http://42pixels.com
 * @package       slugger
 * @subpackage    slugger.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 App::uses('CakeRoute', 'Routing/Route');

/**
 * Sluggable Route
 *
 * Automatically slugs routes based on named parameters
 *
 * @package       slugger
 * @subpackage    slugger.Lib
 * @link http://42pixels.com/blog/slugs-ugly-bugs-pretty-urls
 * @link http://mark-story.com/posts/view/using-custom-route-classes-in-cakephp
 */
class SluggableRoute extends CakeRoute {

/**
 * Override the parsing function to find an id based on a slug
 *
 * @param string $url Url string
 * @return boolean
 */
    public function parse($url) {
		$params = parent::parse($url);

		if (empty($params)) {
			return false;
		}

		if (isset($this->options['models']) && !empty($params['pass'])) {
			foreach ($this->options['models'] as $checkNamed => $slugField) {
				if (is_numeric($checkNamed)) {
					$checkNamed = $slugField;
					$slugField = null;
				}
				$slugSet = $this->getSlugs($checkNamed, $slugField);
				if (empty($slugSet)) {
					continue;
				}
				$slugSet = array_flip($slugSet);
				foreach ($params['pass'] as $key => $pass) {
					if (isset($slugSet[$pass])) {
						unset($params['pass'][$key]);
						$params['named'][$checkNamed] = $slugSet[$pass];
					}
				}
			}
			return $params;
		}
		
		return false;
	}

/**
 * Matches the model's id and converts it to a slug
 *
 * @param array $url Cake url array
 * @return boolean
 */
	public function match($url) {
		if (isset($this->options['models'])) {
			foreach ($this->options['models'] as $checkNamed => $slugField) {
				if (is_numeric($checkNamed)) {
					$checkNamed = $slugField;
					$slugField = null;
				}
				if (isset($url[$checkNamed])) {
					$slugSet = $this->getSlugs($checkNamed, $slugField);
					if (empty($slugSet)) {
						continue;
					}
					if (isset($slugSet[$url[$checkNamed]])) {
						$url[] = $slugSet[$url[$checkNamed]];
						unset($url[$checkNamed]);
					}
				}
			}
		}
		
		return parent::match($url);
	}

/**
 * Slugs a string for the purpose of this route
 *
 * @param array $slug The slug array (containing keys '_field' and '_count')
 * @return string
 */
	public function slug($slug) {
		$str = $slug['_field'];
		if ($slug['_count'] > 1 || (isset($this->options['prependPk']) && $this->options['prependPk'])) {
			$str = $slug['_pk'].' '.$str;
		}
		return $this->_slug($str);
	}

/**
 * Slugs a string using a custom function if defined. If no custom function is
 * defined, it defaults to a strtolower'd `Inflector::slug($str, '-');`
 *
 * @param string $str The string to slug
 * @param string $str Replacement character
 * @return string
 */
	protected function _slug($str, $replacement = '-') {
		if (isset($this->options['slugFunction'])) {
			return call_user_func($this->options['slugFunction'], $str);
		}
		return strtolower(Inflector::slug($str, $replacement));
	}

/**
 * Gets slugs from cache and store in variable for this request
 *
 * @param string $modelName The name of the model
 * @param string $field The field to pull
 * @return array Array of slugs
 */
	public function getSlugs($modelName, $field = null) {
		$cacheConfig = $this->_initSluggerCache();
		if (!isset($this->{$modelName.'_slugs'})) {
			$this->{$modelName.'_slugs'} = Cache::read($modelName.'_slugs', $cacheConfig);
		}
		if (empty($this->{$modelName.'_slugs'})) {
			$Model = ClassRegistry::init($modelName);
			if ($Model === false) {
				return false;
			}
			if (!$field) {
				$field = $Model->displayField;
			}
			$slugs = $Model->find('all', array(
				'fields' => array(
					$Model->name.'.'.$Model->primaryKey,
					$Model->name.'.'.$field,
				),
				'recursive' => -1
			));
			$counts = $Model->find('all', array(
				'fields' => array(					
					'LOWER(TRIM('.$Model->name.'.'.$field.')) AS '.$field,
					'COUNT(*) AS count'
				),
				'group' => array(
					$field
				)
			));
			$counts = Set::combine($counts, '{n}.0.'.$field, '{n}.0.count');
			$listedSlugs = array();
			foreach ($slugs as $pk => $fields) {
				$values = array(
					'_field' => $fields[$Model->name][$field],
					'_count' => $counts[strtolower(trim($fields[$Model->name][$field]))],
					'_pk' => $fields[$Model->name][$Model->primaryKey]
				);
				$listedSlugs[$fields[$Model->name][$Model->primaryKey]] = $this->slug($values);
			}
			Cache::write($modelName.'_slugs', $listedSlugs, $cacheConfig);
			$this->{$modelName.'_slugs'} = $listedSlugs;
		}
		
		return $this->{$modelName.'_slugs'};
	}

/**
 * Invalidate cached slugs for a given model or entry
 *
 * @param string $modelName Name of the model to invalidate cache for
 * @param string $id If of the only entry to update
 * @return boolean True if the value was succesfully deleted, false if it didn't exist or couldn't be removed
 */
	public function invalidateCache($modelName, $id = null) {
		$cacheConfig = $this->_initSluggerCache();

		if (is_null($id)) {
			$result = Cache::delete($modelName.'_slugs', $cacheConfig);
			unset($this->{$modelName.'_slugs'});
		} else {
			$slugs = Cache::read($modelName.'_slugs', $cacheConfig);
			if ($slugs === false) {
				$result = false;
			} else {
				$slugs[$id] = $this->generateSlug($modelName, $id);
				if ($slugs[$id] === false) {
					unset($slugs[$id]);
				}
				if (isset($this->{$modelName.'_slugs'}) && $slugs[$id] !== false) {
					$this->{$modelName.'_slugs'}[$id] = $slugs[$id];
				}
				$result = Cache::write($modelName.'_slugs', $slugs, $cacheConfig);
			}
		}

		return $result;
	}

/**
 * Generates a slug for a given model and id from the database
 *
 * @param string $modelName The name of the model
 * @param string $id Id of the entry to generate a slug for
 * @return mixed False if the config is not found for this model or the entry
 *	does not exist. The generated slug otherwise
 */
	public function generateSlug($modelName, $id) {
		$slug = false;

		if (isset($this->options['models'])) {
			if (array_key_exists($modelName, $this->options['models'])) {
				$slugField = $this->options['models'][$modelName];
			} elseif (array_search($modelName, $this->options['models']) !== false) {
				$slugField = false;
			}

			if (isset($slugField)) {
				$Model = ClassRegistry::init($modelName);
				if ($Model !== false) {
					if (!$slugField) {
						$slugField = $Model->displayField;
					}
					$text = $Model->field($slugField, array(
						$Model->name.'.'.$Model->primaryKey => $id
					));
					if ($text !== false) {
						$count = $Model->find('count', array(
							'conditions' => array($Model->name.'.'.$slugField => $text)
						));
						$values = array('_field' => $text, '_count' => $count, '_pk' => $id);
						$slug = $this->slug($values);
					}
				}
			}
		}

		return $slug;
	}

/**
 * Sets up cache config and returns config name
 * 
 * @return string Cache config name
 */
	protected function _initSluggerCache() {
		Cache::config('Slugger', array(
			'engine' => 'File',
			'duration' => '+1 days',
			'prefix' => 'slugger_'
		));
		return 'Slugger';
	}
}

?>