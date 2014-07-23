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

App::uses('Router', 'Routing');
App::uses('CakeRoute', 'Routing/Route');
App::uses('I18nRoute', 'I18n.Routing/Route');

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
 * Test the construction of an i18nRoute
 *
 * @return void
 * @access public
 */
	public function testConstruction() {
		$initalRouteCount = count(Router::$routes);
		$route = new I18nRoute('/:controller/:action/:id', array(), array('id' => '[0-9]+'));

		$this->assertEquals($route->template, '/:lang/:controller/:action/:id');
		$this->assertEquals($route->defaults, array());
		$this->assertEquals($route->options, array('id' => '[0-9]+', 'lang' => 'eng|fre|spa', '__promote' => true));
		$this->assertFalse($route->compiled());

		$routeCount = count(Router::$routes);
		$this->assertEquals($routeCount, $initalRouteCount + 1);
		$defaultRoute = array_pop(Router::$routes);
		$this->assertEquals($defaultRoute->template, '/:controller/:action/:id');
		$this->assertEquals($defaultRoute->defaults, array(
			'lang' => $this->__defaultLang,
			'action' => 'index',
			'plugin' => null));
		$this->assertFalse($defaultRoute->compiled());
	}

/**
 * Test the construction of an i18nRoute with an explicit lang named param
 *
 * @return void
 * @access public
 */
	public function testConstructionExplicitLang() {
		$initalRouteCount = count(Router::$routes);
		$route = new I18nRoute('/:controller/:action/:id/:lang', array(), array('id' => '[0-9]+'));

		$this->assertEquals($route->template, '/:controller/:action/:id/:lang');
		$this->assertEquals($route->defaults, array());
		$this->assertEquals($route->options, array('id' => '[0-9]+', 'lang' => 'eng|fre|spa'));
		$this->assertFalse($route->compiled());

		$routeCount = count(Router::$routes);
		$this->assertEquals($routeCount, $initalRouteCount);
	}

/**
 * test that routes match their pattern.
 *
 * @return void
 */
	public function testMatchBasic() {
		$route = new I18nRoute('/:controller/:action/:id', array('plugin' => null));
		$result = $route->match(array('controller' => 'posts', 'action' => 'view', 'plugin' => null));
		$this->assertFalse($result);

		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 0));
		$this->assertFalse($result);

		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => 1));
		$this->assertEquals($result, '/spa/posts/view/1');

		$result = $route->match(array('plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => 1, 'lang' => 'fre'));
		$this->assertEquals($result, '/fre/posts/view/1');


		$route = new I18nRoute('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'home'));
		$this->assertEquals($result, '/spa');

		$route = new I18nRoute('/pages/*', array('controller' => 'pages', 'action' => 'display'));
		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'home'));
		$this->assertEquals($result, '/spa/pages/home');

		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'about'));
		$this->assertEquals($result, '/spa/pages/about');


		$route = new I18nRoute('/blog/:action', array('controller' => 'posts'));
		$result = $route->match(array('controller' => 'posts', 'action' => 'view'));
		$this->assertEquals($result, '/spa/blog/view');

		$result = $route->match(array('controller' => 'nodes', 'action' => 'view'));
		$this->assertFalse($result);


		$route = new I18nRoute('/foo/:controller/:action', array('action' => 'index'));
		$result = $route->match(array('controller' => 'posts', 'action' => 'view'));
		$this->assertEquals($result, '/spa/foo/posts/view');


		$route = new I18nRoute('/admin/subscriptions/:action/*', array(
			'controller' => 'subscribe', 'admin' => true, 'prefix' => 'admin'
		));

		$url = array('controller' => 'subscribe', 'admin' => true, 'action' => 'edit', 1);
		$result = $route->match($url);
		$expected = '/spa/admin/subscriptions/edit/1';
		$this->assertEquals($result, $expected);
	}

	public function testMatch_ShouldNotRemoveDefaultLangWhenContainedInTemplate() {
		$route = new I18nRoute('/:lang/pages/*', array('controller' => 'pages', 'action' => 'display'));
		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'home', 'lang' => $this->__defaultLang));
		$this->assertEquals('/' . $this->__defaultLang . '/pages/home', $result);
	}

	public function testMatch_ShouldNotRemoveDefaultLangWhenUsedInRouteContent() {
		$routeStartsChunk = '/' . $this->__defaultLang . '/pages';
		$route = new I18nRoute(
			$routeStartsChunk . '/*',
			array('controller' => 'pages', 'action' => 'display', 'lang' => $this->__defaultLang),
			array('disableAutoNamedLang' => true)
		);
		$result = $route->match(array('controller' => 'pages', 'action' => 'display', 'home', 'lang' => $this->__defaultLang));
		$this->assertEquals($routeStartsChunk . '/home', $result);
	}

/**
 * Testing pagination urls with translation
 *
 * @return void
 * @link https://github.com/CakeDC/i18n/issues/5
 */
	public function testPaginationLinks() {
		Router::connect(':lang/admin/:controller/:action/*', array('action' => 'index', 'admin' => true), array('routeClass' => 'I18nRoute'));
		$result = Router::parse('/eng/admin/posts/index/page:1/limit:3');
		$expected = array (
			'lang' => 'eng',
			'controller' => 'posts',
			'action' => 'index',
			'named' => array (
				'page' => '1',
				'limit' => '3',),
			'pass' => array (),
			'admin' => true,
			'plugin' => null);

		$result = Router::url(array(
			'admin' => true,
			'controller' => 'posts',
			'action' => 'index',
			'lang' => 'eng',
			'page' => 1,
			'limit' => 3));
		$this->assertEquals($result, '/eng/admin/posts/index/page:1/limit:3');
	}

/**
 * test that created routes are parsed correctly.
 *
 * @return void
 * @access public
 */
	public function testParsing() {
		Configure::write('Routing.prefixes', array('admin'));
		Router::reload();

		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'), array('routeClass' => 'I18nRoute'));
		include CakePlugin::path('I18n') . 'Config' . DS . 'routes.php';
		/*
		$result = Router::parse('/');
		$expected = array(
			'plugin' => null, 'controller' => 'pages', 'action' => 'display',
			'named' => array(), 'pass' => array('home'), 'lang' => $this->__defaultLang
		);
		$this->assertEquals($expected, $result);
		$this->assertEquals(Configure::read('Config.language'), $this->__defaultLang);

		$result = Router::parse('/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'view',
			'named' => array(), 'pass' => array(42), 'lang' => $this->__defaultLang
		);
		$this->assertEquals($expected, $result);

		$result = Router::parse('/admin/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'admin_view', 'admin' => true
			'named' => array(), 'pass' => array(42), 'lang' => $this->__defaultLang, 'prefix' => 'admin'
		);

		$this->assertEquals($expected, $result);

		$result = Router::parse('/spa');
		$expected = array(
			'plugin' => null, 'controller' => 'pages', 'action' => 'display',
			'named' => array(), 'pass' => array('home'), 'lang' => 'spa'
		);
		$this->assertEquals($expected, $result);
		$this->assertEquals(Configure::read('Config.language'), 'spa');

		*/
		$result = Router::parse('/spa/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'view',
			'named' => array(), 'pass' => array(42), 'lang' => 'spa'
		);
		$this->assertEquals($expected, $result);

		$result = Router::parse('/spa/admin/posts/view/42');
		$expected = array(
			'plugin' => null, 'controller' => 'posts', 'action' => 'admin_view',
			'named' => array(), 'pass' => array(42), 'lang' => 'spa', 'admin' => true, 'prefix' => 'admin'
		);
		$this->assertEquals($result, $expected);
	}

/**
 * Test connecting the default routes with i18n
 * 
 * @return false
 * @access public
 */
	public function testConnectDefaultRoutes() {
		App::build(array(
			'Plugin' => array(
				CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS
			)
		), APP::RESET);
		CakePlugin::loadAll();
		Configure::write('Routing.prefixes', array('admin'));
		Router::reload();
		include CakePlugin::path('I18n') . 'Config' . DS . 'routes.php';


		$result = Router::url(array('plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index'));
		$this->assertEquals($result, '/spa/plugin_js/js_file');

		$result = Router::url(array('plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index', 'admin' => true));
		$this->assertEquals($result, '/spa/admin/plugin_js/js_file');

		$result = Router::parse('/plugin_js/js_file');
		$expected = array(
			'plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index',
			'named' => array(), 'pass' => array(), 'lang' => $this->__defaultLang
		);
		$this->assertEquals($result, $expected);

		$result = Router::parse('/admin/plugin_js/js_file');
		$expected['prefix'] = 'admin';
		$expected['admin'] = true;
		$expected['action'] = 'admin_index';
		$this->assertEquals($result, $expected);

		$result = Router::parse('/spa/admin/plugin_js/js_file');
		$expected['lang'] = 'spa';
		$this->assertEquals($result, $expected);

		$result = Router::parse('/spa/plugin_js/js_file');
		unset($expected['admin'], $expected['prefix']);
		$expected['action'] = 'index';
		$this->assertEquals($result, $expected);
	}

/**
 * Test reverse routing with i18n
 *
 * @return false
 * @access public
 */
	public function testReverseRouting() {
		Router::reload();
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
		$result = Router::url(array('controller' => 'pages', 'action' => 'display', 'home'));
		$expected = '/';
		$this->assertEquals($result, $expected);
		$result = Router::parse('/');
		$expected = array('named' => array(), 'pass' => array('home'), 'controller' => 'pages', 'action' => 'display', 'plugin' => null);
		$this->assertEquals($result, $expected);

		Configure::write('Config.language', 'eng');
		Configure::write('Config.languages', array('spa'));
		Router::reload();
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'), array('routeClass' => 'I18nRoute'));
		Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'I18nRoute'));
		$result = Router::url(array('controller' => 'pages', 'action' => 'display', 'home'));
		$expected = '/';
		$this->assertEquals($result, $expected);
		$result = Router::parse($result);
		$expected = array('named' => array(), 'pass' => array('home'), 'controller' => 'pages', 'action' => 'display', 'plugin' => null);
		$this->assertEquals($result, $expected);
	}
}
