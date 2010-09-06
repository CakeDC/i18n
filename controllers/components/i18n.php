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
 * i18n Component
 *
 * @package i18n
 * @subpackage i18n.controllers.components
 */
class I18nComponent extends Object {

/**
 * Components
 *
 * @var array $components
 */
	public $components = array('Cookie', 'Session');

/**
 * Controller
 *
 * @var mixed $controller
 */
	public $controller = null;

/**
 * Initialize Callback
 *
 * @param object
 * @return void
 */
	public function initialize(Controller $controller, $settings = array()) {
		$this->Controller = $controller;
		if (!empty($settings['set'])) {
			$this->setLanguage();
		}
	}

/**
 * Sets the language
 *
 * @return void
 */
	public function setLanguage() {
		static $L10n = null;

		if (is_null($L10n)) {
			App::import('Core', 'L10n');
			$L10n = new L10n();
		}

		if (!isset($this->Controller->params['lang']) || empty($this->Controller->params['lang'])) {
			if ($this->Session->check('Config.language')) {
				$L10n->get($this->Session->read('Config.language'));
			} else {
				$L10n->get(Configure::read('Config.language'));
			}
		} else {
			$L10n->get($this->Controller->params['lang']);
		}

		$lang = $L10n->catalog($L10n->lang);
		$this->Controller->params['lang'] = $lang['localeFallback'];
		if (isset($this->Controller->params['lang']) && $this->Controller->params['lang'] != 'eng') {
			Router::connectNamed(array($this->Controller->params['lang']));
		}
		Configure::write('Config.language', $this->Controller->params['lang']);
		$this->Session->write('Config.language',  $this->Controller->params['lang']);
	}
}
