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

App::import('Lib', 'I18n.I18nRoute');
App::import('Lib', 'I18n.I18nSluggableRoute');

class I18nUser extends Model {
	public $name = 'User';
	public $useTable = 'users';
	public $alias = 'User';
	public $displayField = 'username';
}

/**
 * Test case for I18nSluggableRoute
 *
 */
class I18nSluggableRouteTestCase extends CakeTestCase {

/**
 * Autoload entrypoint for fixtures dependecy solver
 *
 * @var string
 * @access public
 */
	public $plugin = 'i18n';

/**
 * Fixtrues
 *
 * @var array
 * @access public
 */
	public $fixtures = array('plugin.i18n.user');

/**
 * Default language of the application
 * 
 * @var string
 * @access private
 */
	private $__defaultLang = 'eng'; 

/**
 * startTest method
 *
 * @access public
 * @return void
 */
	public function startTest() {
		//Router::connect('/users', array('plugin' => null, 'controller' => 'users', 'action' => 'index'), array('routeClass' => 'I18nRoute'));

		$this->_routing = Configure::read('Routing');
		$this->_config = Configure::read('Config');
		Configure::write('Config.language', 'spa');
		Configure::write('Config.languages', array('eng', 'fre', 'spa'));
		Configure::write('Routing', array('admin' => null, 'prefixes' => array()));
		
		if (defined('DEFAULT_LANGUAGE')) {
			$this->__defaultLang = DEFAULT_LANGUAGE;
		} else {
			define('DEFAULT_LANGUAGE', $this->__defaultLang);
		}
		
		I18nRoute::reload();
	}

/**
 * end the test and reset the environment
 *
 * @return void
 * @access public
 */
	public function endTest() {
		Configure::write('Routing', $this->_routing);
		Configure::write('Config', $this->_config);
	}

/**
 * test that routes match their pattern.
 *
 * @return void
 */
	public function testMatchBasic() {
		$route = new I18nSluggableRoute(
			'/users/view/*',
			array('plugin' => null, 'controller' => 'users', 'action' => 'view'),
			array('models' => array('I18nUser'))
		);
		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 0));
		$this->assertFalse($result);

		$result = $route->match(array('controller' => 'users', 'action' => 'view', 'plugin' => null, 'user-1'));
		$this->assertEqual($result, '/spa/users/view/phpnut');

		$result = $route->match(array('lang' => 'fre', 'controller' => 'users', 'action' => 'view', 'plugin' => null, 'user-1'));
		$this->assertEqual($result, '/fre/users/view/phpnut');
	}

/**
 * test that created routes are parsed correctly.
 *
 * @return void
 * @access public
 */
	public function testParsing() {
		$route = Router::connect(
			'/users/view/*',
			array('plugin' => null, 'controller' => 'users', 'action' => 'view'),
			array('routeClass' => 'I18nSluggableRoute', 'models' => array('I18nUser'))
		);

		$result = Router::parse('/users/view/phpnut');
		$expected = array(
			'plugin' => null, 'controller' => 'users', 'action' => 'view',
			'pass' => array('user-1'),
			'named' => array(),
			'lang' => $this->__defaultLang
		);

		$this->assertEqual($result, $expected);
		$this->assertEqual(Configure::read('Config.language'), $this->__defaultLang);
		
		$result = Router::parse('/fre/users/view/phpnut');
		$expected = array(
			'plugin' => null, 'controller' => 'users', 'action' => 'view',
			'pass' => array('user-1'), 'lang' => 'fre', 'named' => array()
		);
		$this->assertEqual($result, $expected);

		$result = Router::parse('/users/view/invalid-user');
		$expected = array(
			'plugin' => null, 'controller' => 'users', 'action' => 'view',
			'named' => array(), 'pass' => array('invalid-user'),
			'lang' => $this->__defaultLang
		);
		$this->assertEqual($result, $expected);
	}

}