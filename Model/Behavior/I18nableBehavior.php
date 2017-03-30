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

/**
 * i18nable Behavior
 *
 * @package		i18n
 * @subpackage	i18n.models.behaviors
 */
class I18nableBehavior extends ModelBehavior {

/**
 * Default settings
 *
 * @var array
 */
	public $defaults = array(
		'languageField' => 'language_id'
	);

/**
 * Settings array
 *
 * @var array
 */
	public $settings = array();

/**
 * Setup
 *
 * @param Model $Model
 * @param array $settings
 */
	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $this->defaults;
		}
		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], is_array($settings) ? $settings : array());
	}

/**
 * Add language filter
 *
 * @param Model $Model
 * @param array $query
 * @return array|bool
 */
	public function beforeFind(Model $Model, $query) {
		if (empty($this->settings[$Model->alias])) {
			return;
		}
		$settings = $this->settings[$Model->alias];
		$language = Configure::read('Config.language');
		if ($Model->hasField($settings['languageField']) && (!isset($query['ignoreLanguage']))) {
			if (empty($query['conditions'][$Model->alias . '.' . $settings['languageField']])) {
				if (isset($query['language'])) {
					$language = $query['language'];
				}
				$query['conditions'][$Model->alias . '.' . $settings['languageField']] = $language;
			}
		}
		return $query;
	}

/**
 * Set current language 
 *
 * @param Model $Model
 * @return void
 */
	public function beforeSave(Model $Model, $options = array()) {
		if (empty($this->settings[$Model->alias])) {
			return;
		}
		$settings = $this->settings[$Model->alias];
		$language = Configure::read('Config.language');
		if ($Model->hasField($settings['languageField'])) {
			if (empty($Model->data[$Model->alias][$settings['languageField']])) {
				$Model->set(array($settings['languageField'] => $language));
			}
		}
		return true;
	}
}
