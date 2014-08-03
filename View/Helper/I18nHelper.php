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
 * i18n Helper
 *
 * i18n view helper allowing to easily generate common i18n related controls
 *
 * @package i18n
 * @subpackage i18n.views.helpers
 */
class I18nHelper extends AppHelper {

/**
 * Helpers
 *
 * @var array $helpers
 */
	public $helpers = array('Html');

/**
 * Base path for the flags images, with a trailing slash
 * 
 * @var string $basePath
 */
	public $basePath = '/i18n/img/flags/';

/**
 * Displays a list of flags
 * 
 * @param array $options Options with the following possible keys
 * 	- basePath: Base path for the flag images, with a trailing slash
 * 	- class: Class of the <ul> wrapper
 * 	- id: Id of the wrapper
 *  - appendName: boolean, whether the language name must be appended to the flag or not [default: false]
 * @return void
 */
	public function flagSwitcher($options = array()) {
		$_defaults = array(
			'basePath' => $this->basePath,
			'class' => 'languages',
			'id' => '',
			'appendName' => false);
		$options = array_merge($_defaults, $options);
		$langs = $this->availableLanguages();

		$out = '';
		if (!empty($langs)) {
			$id = empty($options['id']) ? '' : ' id="' . $options['id'] . '"';
			$out .= '<ul class="' . $options['class'] . '"' . $id . '>';
			foreach ($langs as $lang) {
				$class = $lang;
				if ($lang == Configure::read('Config.language')) {
					$class .= ' selected';
				}
				$url = array_merge($this->params['named'], $this->params['pass'], compact('lang'));
				$out .= '<li class="' . $class . '">' .
					$this->Html->link($this->flagImage($lang, $options), $url, array('escape' => false)) .
				'</li>';
			}
			$out .= '</ul>';
		}

		return $out;
	}

/**
 * Returns the correct image from the language code
 * 
 * @param string $lang Long language code
 * @param array $options Options with the following possible keys
 * 	- basePath: Base path for the flag images, with a trailing slash
 *  - appendName: boolean, whether the language name must be appended to the flag or not [default: false]
 * @return string Image markup
 */
	public function flagImage($lang, $options = array()) {
		$L10n = $this->_getCatalog();
		$_defaults = array('basePath' => $this->basePath, 'appendName' => false);
		$options = array_merge($_defaults, $options);

		if (strlen($lang) == 3) {
			$flag = $L10n->map($lang);
		} else {
			$flag = $lang;
		}

		if ($flag === false) {
			$flag = $lang;
		}

		if (strpos($lang, '-') !== false) {
			$pieces = explode('-', $lang);
			$flag = array_pop($pieces);
		}

		$result = $this->Html->image($options['basePath'] . $flag . '.png');

		if ($options['appendName'] === true) {
			$result .= $this->Html->tag('span', $this->getName($lang));
		}
		return $result;
	}

/**
 * Returns all the available languages on the website
 *
 * @param boolean $includeCurrent Whether or not the current language must be included in the result
 * @param bool    $realNames
 * @return array List of available language codes
 */
	public function availableLanguages($includeCurrent = true, $realNames = false) {
		$languages = Configure::read('Config.languages');
		if (defined('DEFAULT_LANGUAGE') && false === array_search(DEFAULT_LANGUAGE, $languages)) {
			array_unshift($languages, DEFAULT_LANGUAGE);
		}

		if (!$includeCurrent && in_array(Configure::read('Config.language'), $languages)) {
			unset($languages[array_search(Configure::read('Config.language'), $languages)]);
		}

		if ($realNames) {
			$langs = $languages;
			$languages = array();
			foreach ($langs as $l) {
				$languages[] = $this->getName($l);
			}
		}
		return $languages;
	}

/**
 * Returns the readable name of a language code
 *
 * @param string $code language three letters code
 * @return string language name
 */
	public function getName($code) {
		$langData = $this->_getCatalog()->catalog($code);
		return $langData['language'];
	}

/**
 * Returns a L10n instance
 *
 * @return L10n instance
 */
	protected function _getCatalog() {
		if (empty($this->L10n)) {
			App::import('Core', 'L10n');
			$this->L10n = new L10n();
		}
		return $this->L10n;
	}
}
