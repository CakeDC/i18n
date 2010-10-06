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

App::import('Lib', array('I18n.I18nRoute'));

/**
 * Test case for i18nroute
 *
 * @package i18n
 * @author i18n.test.cases.libs
 */
class I18nRouteTestCase extends CakeTestCase {
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
 * Test the construction of an i18nRoute
 *
 * @return void
 * @access public
 */
	public function testConstruction() {
		$Router = Router::getInstance();
		$initalRouteCount = count($Router->routes);
		$route = new I18nRoute('/:controller/:action/:id', array(), array('id' => '[0-9]+'));

		$this->assertEqual($route->template, '/:lang/:controller/:action/:id');
		$this->assertEqual($route->defaults, array());
		$this->assertEqual($route->options, array('id' => '[0-9]+', 'lang' => 'eng|fre|spa'));
		$this->assertFalse($route->compiled());

		$routeCount = count($Router->routes);
		$this->assertEqual($routeCount, $initalRouteCount + 1);
		$defaultRoute = array_pop($Router->routes);
		$this->assertEqual($defaultRoute->template, '/:controller/:action/:id');
		$this->assertEqual($defaultRoute->defaults, array(
			'lang' => $this->__defaultLang,
			'action' => 'index',
			'plugin' => null));
		$this->assertEqual($defaultRoute->options, array('id' => '[0-9]+'));
		$this->assertFalse($defaultRoute->compiled());
	}

/**
 * Test the construction of an i18nRoute with an explicit lang named param
 *
 * @return void
 * @access public
 */
	public function testConstructionExplicitLang() {
		$Router = Router::getInstance();
		$initalRouteCount = count($Router->routes);
		$route = new I18nRoute('/:controller/:action/:id/:lang', array(), array('id' => '[0-9]+'));

		$this->assertEqual($route->template, '/:controller/:action/:id/:lang');
		$this->assertEqual($route->defaults, array());
		$this->assertEqual($route->options, array('id' => '[0-9]+', 'lang' => 'eng|fre|spa'));
		$this->assertFalse($route->compiled());

		$routeCount = count($Router->routes);
		$this->assertEqual($routeCount, $initalRouteCount + 1);
		$defaultRoute = array_pop($Router->routes);
		$this->assertEqual($defaultRoute->template, '/:controller/:action/:id');
		$this->assertEqual($defaultRoute->defaults, array(
			'lang' => $this->__defaultLang,
			'action' => 'index',
			'plugin' => null));
		$this->assertEqual($defaultRoute->options, array('id' => '[0-9]+'));
		$this->assertFalse($defaultRoute->compiled());
	}

/**
 * test that routes match their pattern.
 *
 * @return void
 */
	function testMatchBasic() {
		$route = new I18nRoute('/:controller/:action/:id', array('plugin' => null));
		$result = $route->match(array('controller' => 'posts', 'action' => 'view', 'plugin' => null));
		$this->assertFalse($result);

		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 0));
		$this->assertFalse($result);

		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => 1));
		$this->assertEqual($result, '/spa/posts/view/1');
		
		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => 1, 'lang' => 'fre'));
		$this->assertEqual($result, '/fre/posts/view/1');


		$route = new I18nRoute('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'home'));
		$this->assertEqual($result, '/spa');

		$route = new I18nRoute('/pages/*', array('controller' => 'pages', 'action' => 'display'));
		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'home'));
		$this->assertEqual($result, '/spa/pages/home');

		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'about'));
		$this->assertEqual($result, '/spa/pages/about');


		$route = new I18nRoute('/blog/:action', array('controller' => 'posts'));
		$result = $route->match(array('controller' => 'posts', 'action' => 'view'));
		$this->assertEqual($result, '/spa/blog/view');

		$result = $route->match(array('controller' => 'nodes', 'action' => 'view'));
		$this->assertFalse($result);


		$route = new I18nRoute('/foo/:controller/:action', array('action' => 'index'));
		$result = $route->match(array('controller' => 'posts', 'action' => 'view'));
		$this->assertEqual($result, '/spa/foo/posts/view');


		$route = new I18nRoute('/admin/subscriptions/:action/*', array(
			'controller' => 'subscribe', 'admin' => true, 'prefix' => 'admin'
		));

		$url = array('controller' => 'subscribe', 'admin' => true, 'action' => 'edit', 1);
		$result = $route->match($url);
		$expected = '/spa/admin/subscriptions/edit/1';
		$this->assertEqual($result, $expected);
	}

/**
 * test that created routes are parsed correctly.
 *
 * @return void
 * @access public
 */
	public function testParsing() {
		Configure::write('Routing.prefixes', array('admin'));
		I18nRoute::reload();
		Router::defaults(false);

		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'), array('routeClass' => 'I18nRoute'));
		Router::connect('/admin/:controller/:action/*', array('action' => 'index', 'admin' => true), array('routeClass' => 'I18nRoute'));
		Router::connect('/:controller/:action/*', array('action' => 'index'), array('routeClass' => 'I18nRoute'));
		// This call is needed to work since the "default language" route is created from the constructor
		I18nRoute::promoteLangRoutes();

		$result = Router::parse('/');
		$expected = array(
			'plugin' => null, 'controller' => 'pages', 'action' => 'display',
			'named' => array(), 'pass' => array('home'), 'lang' => $this->__defaultLang
		);
		$this->assertEqual($result, $expected);
		$this->assertEqual(Configure::read('Config.language'), $this->__defaultLang);
		
		$result = Router::parse('/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'view',
			'named' => array(), 'pass' => array(42), 'lang' => $this->__defaultLang
		);
		$this->assertEqual($result, $expected);

		$result = Router::parse('/admin/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'view',
			'named' => array(), 'pass' => array(42), 'lang' => $this->__defaultLang,
			'admin' => true, 'prefix' => 'admin'
		);
		$this->assertEqual($result, $expected);
		

		$result = Router::parse('/spa');
		$expected = array(
			'plugin' => null, 'controller' => 'pages', 'action' => 'display',
			'named' => array(), 'pass' => array('home'), 'lang' => 'spa'
		);
		$this->assertEqual($result, $expected);
		$this->assertEqual(Configure::read('Config.language'), 'spa');
		
		
		$result = Router::parse('/spa/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'view',
			'named' => array(), 'pass' => array(42), 'lang' => 'spa'
		);
		$this->assertEqual($result, $expected);

		$result = Router::parse('/spa/admin/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'view',
			'named' => array(), 'pass' => array(42), 'lang' => 'spa', 'admin' => true, 'prefix' => 'admin'
		);
		$this->assertEqual($result, $expected);
	}

/**
 * Test connecting the default routes with i18n
 * 
 * @return false
 * @access public
 */
	public function testConnectDefaultRoutes() {
		App::build(array(
			'plugins' =>  array(
				TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS
			)
		), true);
		App::objects('plugin', null, false);
		Configure::write('Routing.prefixes', array('admin'));
		Router::reload();
		I18nRoute::connectDefaultRoutes();


		$result = Router::url(array('plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index'));
		$this->assertEqual($result, '/spa/plugin_js/js_file');
		
		$result = Router::url(array('plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index', 'admin' => true));
		$this->assertEqual($result, '/spa/admin/plugin_js/js_file');

		
		$result = Router::parse('/plugin_js/js_file');
		$expected = array(
			'plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index',
			'named' => array(), 'pass' => array(), 'lang' => $this->__defaultLang
		);
		$this->assertEqual($result, $expected);
		
		$result = Router::parse('/admin/plugin_js/js_file');
		$expected['prefix'] = 'admin';
		$expected['admin'] = true;
		$this->assertEqual($result, $expected);
		
		$result = Router::parse('/spa/admin/plugin_js/js_file');
		$expected['lang'] = 'spa';
		$this->assertEqual($result, $expected);

		$result = Router::parse('/spa/plugin_js/js_file');
		unset($expected['admin'], $expected['prefix']);
		$this->assertEqual($result, $expected);


		// Short plugin syntax
		$result = Router::url(array('plugin' => 'test_plugin', 'controller' => 'test_plugin', 'action' => 'index'));
		$this->assertEqual($result, '/spa/test_plugin');

		$result = Router::parse('/test_plugin');
		$expected = array(
			'plugin' => 'test_plugin', 'controller' => 'test_plugin', 'action' => 'index',
			'named' => array(), 'pass' => array(), 'lang' => $this->__defaultLang
		);
		$this->assertEqual($result, $expected, 'Plugin shortcut route broken. %s');

		$result = Router::parse('/spa/test_plugin');
		$expected = array(
			'plugin' => 'test_plugin', 'controller' => 'test_plugin', 'action' => 'index',
			'named' => array(), 'pass' => array(), 'lang' => 'spa'
		);
		$this->assertEqual($result, $expected, 'Plugin shortcut route broken. %s');
	}

/**
 * Test that lang route promotion makes a correct permutation with the default language
 * 
 * @return void
 * @access public
 */
	public function testPromoteLangRoutes() {
		$Router = Router::getInstance();

		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'), array('routeClass' => 'I18nRoute'));
		Router::connect('/:controller/:action/*', array('action' => 'index'), array('routeClass' => 'I18nRoute'));
		Router::connect('/admin/:controller/:action/*', array('action' => 'index', 'admin' => true), array('routeClass' => 'I18nRoute'));
		Router::connect('/foo', array('controller' => 'foo', 'action' => 'index'));

		$this->assertEqual(count($Router->routes), 7);
		$beforePromotionRoutes = $Router->routes;
		I18nRoute::promoteLangRoutes();

		$Router = Router::getInstance();
		$this->assertIdentical($Router->routes[0], $beforePromotionRoutes[1]);
		$this->assertIdentical($Router->routes[1], $beforePromotionRoutes[0]);
		$this->assertIdentical($Router->routes[2], $beforePromotionRoutes[3]);
		$this->assertIdentical($Router->routes[3], $beforePromotionRoutes[2]);
		$this->assertIdentical($Router->routes[4], $beforePromotionRoutes[5]);
		$this->assertIdentical($Router->routes[5], $beforePromotionRoutes[4]);
		$this->assertIdentical($Router->routes[6], $beforePromotionRoutes[6]);
	}

}

/**
 * Test case for PluginShortI18nRoute
 *
 * @package i18n
 * @author i18n.test.cases.libs
 */
class PluginShortI18nRouteTestCase extends CakeTestCase {
/**
 * Default language of the application
 * 
 * @var string
 */
	private $__defaultLang = 'eng'; 
	
/**
 * startTest method
 *
 * @return void
 */
	public function startTest() {
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
		
		PluginShortI18nRoute::reload();
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
 * test the parsing of routes.
 *
 * @return void
 */
	public function testParsing() {
		Router::defaults(false);
		Router::connect('/:plugin', array('action' => 'index'), array('routeClass' => 'PluginShortI18nRoute', 'plugin' => 'foo|bar'));
		// This call is needed to work since the "default language" route is created from the constructor
		PluginShortI18nRoute::promoteLangRoutes();

		$result = Router::parse('/foo');
		$this->assertEqual($result['plugin'], 'foo');
		$this->assertEqual($result['controller'], 'foo');
		$this->assertEqual($result['action'], 'index');
		$this->assertEqual($result['lang'], $this->__defaultLang);

		$result = Router::parse('/spa/foo');
		$this->assertEqual($result['plugin'], 'foo');
		$this->assertEqual($result['controller'], 'foo');
		$this->assertEqual($result['action'], 'index');
		$this->assertEqual($result['lang'], 'spa');

		$result = Router::parse('/wrong');
		$this->assertTrue(empty($result['plugin']), 'Wrong plugin name matched %s');
	}

/**
 * test the reverse routing of the plugin shortcut urls.
 *
 * @return void
 */
	function testMatch() {
		$route = new PluginShortI18nRoute('/:plugin', array('action' => 'index'), array('plugin' => 'foo|bar'));

		$result = $route->match(array('plugin' => 'foo', 'controller' => 'posts', 'action' => 'index'));
		$this->assertFalse($result, 'plugin controller mismatch was converted. %s');

		$result = $route->match(array('plugin' => 'foo', 'controller' => 'foo', 'action' => 'index'));
		$this->assertEqual($result, '/spa/foo');
	}
}