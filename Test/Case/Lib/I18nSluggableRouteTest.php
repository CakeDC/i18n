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

App::uses('Model', 'Model');
App::uses('Router', 'Routing');
App::uses('CakeRoute', 'Routing/Route');
App::uses('I18nRoute', 'I18n.Routing/Route');
App::uses('I18nSluggableRoute', 'I18n.Routing/Route');

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
 * Fixtrues
 *
 * @var array
 * @access public
 */
	public $fixtures = array(
		'plugin.i18n.user'
	);

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
	public function setUp() {
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
		Router::reload();
	}

/**
 * test that routes match their pattern.
 *
 * @return void
 */
	public function testMatchBasic() {
		$route = new I18nSluggableRoute(
			'/users/view/:I18nUser',
			array('plugin' => null, 'controller' => 'users', 'action' => 'view'),
			array('models' => array('I18nUser'))
		);
		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 0));
		$this->assertFalse($result);

		$result = $route->match(array('controller' => 'users', 'action' => 'view', 'plugin' => null, 'user-1'));
		$this->assertEquals($result, '/spa/users/view/phpnut');

		$result = $route->match(array('lang' => 'fre', 'controller' => 'users', 'action' => 'view', 'plugin' => null, 'user-1'));
		$this->assertEquals($result, '/fre/users/view/phpnut');
	}

/**
 * test that created routes are parsed correctly.
 *
 * @return void
 * @access public
 */
	public function testParsing() {
		$route = Router::connect(
			'/users/view/:I18nUser',
			array('plugin' => null, 'controller' => 'users', 'action' => 'view'),
			array('routeClass' => 'I18nSluggableRoute', 'models' => array('I18nUser'))
		);

		$result = Router::parse('/users/view/phpnut');
		$expected = array(
			'plugin' => null, 'controller' => 'users', 'action' => 'view',
			'pass' => array('user-1'),
			'named' => array(),
			'I18nUser' => 'phpnut',
			'lang' => $this->__defaultLang
		);

		$this->assertEquals($result, $expected);
		$this->assertEquals(Configure::read('Config.language'), $this->__defaultLang);
		
		$result = Router::parse('/fre/users/view/phpnut');
		$expected = array(
			'plugin' => null, 'controller' => 'users', 'action' => 'view',
			'pass' => array('user-1'), 'lang' => 'fre', 'named' => array(),
			'I18nUser' => 'phpnut'
		);
		$this->assertEquals($result, $expected);

		$result = Router::parse('/users/view/invalid-user');
		$expected = array(
			'plugin' => null, 'controller' => 'users', 'action' => 'view',
			'named' => array(), 'pass' => array('invalid-user'),
			'lang' => $this->__defaultLang
		);
		$this->assertEmpty($result);
	}

}