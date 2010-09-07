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

App::import('Component', array('I18n.I18n', 'Session'));
App::import('Model', 'Model');
Mock::generate('Controller', 'TestController');
Mock::generate('Model', 'MockModel', array());

/**
 * i18n Component test
 *
 * @package i18n
 * @subpackage i18n.tests.cases.components
 */
class I18nComponentTestCase extends CakeTestCase {

/**
 * Start Test callback
 *
 * @param string $method
 * @return void
 * @access public
 */
	public function startTest($method) {
		parent::startTest($method);
		Configure::write('Cache.disable', true);
		$this->Controller = new TestController;
		$this->Controller->Test = new MockModel;
		ClassRegistry::removeObject('test');
		ClassRegistry::addObject('test', $this->Controller->Test);
		$this->I18n = new I18nComponent($this->Controller);
		$this->I18n->Session = $this->Controller->Session = new SessionComponent();
	}

/**
 * End Test callback
 *
 * @param string $method
 * @return void
 * @access public
 */
	public function endTest($method) {
		parent::endTest($method);
		unset($this->I18n);
		ClassRegistry::flush();
	}

/**
 * Auxiliary method to setup initial expectations and return values
 *
 * @return array
 * @access public
 */
	protected function _setupComponent($options = array()) {		
		$this->I18n->initialize($this->Controller, $options);
	}

/**
 * Tests that the controller reference gets assigned to the component
 *
 * @return array
 * @access public
 */
	public function testInitialize() {
		$this->_setupComponent();
		$this->I18n->Session->write('Config.language', 'eng');
		$this->assertIdentical($this->Controller, $this->I18n->Controller);
	}

/**
 * Tests that language parsed and defined
 *
 * @return array
 * @access public
 */
	public function testInitializeWithSetDefault() {
		$this->I18n->Session->write('Config.language', 'eng');
		Configure::write('Config.language', 'eng');
		$this->_setupComponent(array('set' => true));
		$sessionLang = $this->Controller->Session->read('Config.language');
		$configLang = Configure::write('Config.language', 'eng');;
		$controllerLang = $this->Controller->params['lang'];
		$this->assertEqual($sessionLang, 'eng');
		$this->assertEqual($configLang, 'eng');
		$this->assertEqual($controllerLang, 'eng');
	}

/**
 * Tests that route language used as default
 *
 * @return array
 * @access public
 */
	public function testFrenchLanguagePassed() {
		$this->I18n->Session->write('Config.language', 'eng');
		$this->Controller->params['lang'] = 'fra';
		$this->_setupComponent(array('set' => true));
		$controllerLang = $this->Controller->params['lang'];
		$configLang = Configure::read('Config.language');
		$sessionLang = $this->I18n->Session->read('Config.language');
		$this->assertEqual($sessionLang, 'fre');
		$this->assertEqual($configLang, 'fre');
		$this->assertEqual($controllerLang, 'fre');
	}

/**
 * Tests that session language is not defined and configure language used as default
 *
 * @return array
 * @access public
 */
	public function testNoConfigLanguage() {
		$this->I18n->Session->write('Config.language', 'eng');
		Configure::write('Config.language', null);
		$this->Controller->params['lang'] = null;
		$this->_setupComponent(array('set' => true));
		$controllerLang = $this->Controller->params['lang'];
		$configLang = Configure::read('Config.language');
		$sessionLang = $this->I18n->Session->read('Config.language');
		$this->assertEqual($sessionLang, 'eng');
		$this->assertEqual($configLang, 'eng');
		$this->assertEqual($controllerLang, 'eng');
	}

/**
 * Tests that session language defined and used as default
 *
 * @return array
 * @access public
 */
	public function testNoSessionLanguage() {
		Configure::write('Config.language', 'fre');
		$this->I18n->Session->delete('Config.language');
		$this->Controller->params['lang'] = null;
		$this->_setupComponent(array('set' => true));
		$controllerLang = $this->Controller->params['lang'];
		$configLang = Configure::read('Config.language');
		$sessionLang = $this->I18n->Session->read('Config.language');
		$this->assertEqual($sessionLang, 'fre');
		$this->assertEqual($configLang, 'fre');
		$this->assertEqual($controllerLang, 'fre');
	}

}