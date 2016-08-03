<?php

App::uses('SluggableRoute', 'I18n.Routing/Route');
App::uses('Router', 'Routing');

class SluggableRouteTestCase extends CakeTestCase {

	public $fixtures = array(
		'plugin.i18n.route_test',
		'plugin.i18n.route_two_test'
	);

	public function setUp() {
		parent::setUp();
		Configure::write('Cache.disable', false);
		Router::reload();
		Router::connect('/:controller/:action/*',
			array(),
			array(
				'routeClass' => 'SluggableRoute',
				'models' => array('RouteTest')
			)
		);
		$this->RouteTest = ClassRegistry::init('RouteTest');
	}

	public function tearDown() {
		parent::tearDown();
		Cache::clear(false, 'Slugger');
		Router::reload();
		unset($this->RouteTest);
	}

	public function testCustomSlugFunction() {
		Router::reload();
		Router::connect('/:controller/:action/*',
			array(),
			array(
				'routeClass' => 'SluggableRoute',
				'models' => array('RouteTest'),
				'slugFunction' => array('Inflector', 'slug')
			)
		);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 1
		));
		$expected = '/route_tests/view/A_page_title';
		$this->assertEquals($result, $expected);

		$result = Router::parse('/route_tests/view/A_page_title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTest' => 1
			),
			'plugin' => null,
			'pass' => array()
		);
		$this->assertEquals($result, $expected);

		$Sluggable = new SluggableRoute('/', array(), array('models' => array('RouteTest')));
		$Sluggable->invalidateCache('RouteTest');
		Router::reload();
		Router::connect('/:controller/:action/*',
			array(),
			array(
				'routeClass' => 'SluggableRoute',
				'models' => array('RouteTest'),
				'slugFunction' => create_function('$str', 'return str_replace(" ", "", strtoupper($str));')
			)
		);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 3
		));
		$expected = '/route_tests/view/ILOVECAKEPHP';
		$this->assertEquals($result, $expected);
	}

	public function testGroupingCaseSensitivity() {
		$this->RouteTest->save(array(
			'RouteTest' => array(
				'title' => 'i love cakephp',
				'name' => 'case sensitive grouping, please!',
			)
		));

		$results = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 4
		));
		$expected = '/route_tests/view/4-i-love-cakephp';
		$this->assertEquals($results, $expected);

		$results = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 3
		));
		$expected = '/route_tests/view/3-i-love-cakephp';
		$this->assertEquals($results, $expected);
	}

	public function testGenerateSlug() {
		$Sluggable = new SluggableRoute('/', array(), array('models' => array('RouteTest')));

		$this->assertFalse($Sluggable->generateSlug('RouteTest', 100));

		$results = $Sluggable->generateSlug('RouteTest', 1);
		$this->assertEquals($results, 'a-page-title');

		$this->RouteTest->save(array('RouteTest' => array('title' => 'A page title')));
		$results = $Sluggable->generateSlug('RouteTest', 1);
		$this->assertEquals($results, '1-a-page-title');
		$results = $Sluggable->generateSlug('RouteTest', 4);
		$this->assertEquals($results, '4-a-page-title');
	}

	public function testInvalidateCache() {
		$Sluggable = new SluggableRoute('/', array(), array('models' => array('RouteTest')));

		$Sluggable->getSlugs('RouteTest');
		$varCache = $Sluggable->RouteTest_slugs;
		$this->assertInternalType('array', $varCache);
		$cached = Cache::read('RouteTest_slugs', 'Slugger');
		$this->assertInternalType('array', $cached);

		$Sluggable->invalidateCache('RouteTest');
		$this->assertFalse(isset($Sluggable->RouteTest_slugs));
		$this->assertFalse(Cache::read('RouteTest_slugs', 'Slugger'));

		$Sluggable->getSlugs('RouteTest');
		$this->RouteTest->id = 1;
		$this->RouteTest->saveField('title', 'A different name!');
		$Sluggable->invalidateCache('RouteTest', 1);

		$result = $Sluggable->RouteTest_slugs;
		$expected = array(
			1 => 'a-different-name',
			2 => 'another-title',
			3 => 'i-love-cakephp',
		);
		$this->assertEquals($result, $expected);

		$result = Cache::read('RouteTest_slugs', 'Slugger');
		$expected = array(
			1 => 'a-different-name',
			2 => 'another-title',
			3 => 'i-love-cakephp',
		);
		$this->assertEquals($result, $expected);

		$Sluggable->invalidateCache('RouteTest');
		$Sluggable->invalidateCache('RouteTest', 2);

		$this->assertFalse(isset($Sluggable->RouteTest_slugs));
		$this->assertFalse(Cache::read('RouteTest_slugs', 'Slugger'));
	}

	public function testEmptyTable() {
		$this->RouteTest->deleteAll(array(
			'id >' => 0
		));
		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 1
		));
		$expected = '/route_tests/view/RouteTest:1';
		$this->assertEquals($result, $expected);
	}

	public function testPrependPk() {
		Router::reload();
		Router::connect('/:controller/:action/*',
			array(),
			array(
				'routeClass' => 'SluggableRoute',
				'models' => array('RouteTest'),
				'prependPk' => true
			)
		);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 1
		));
		$expected = '/route_tests/view/1-a-page-title';
		$this->assertEquals($result, $expected);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 2
		));
		$expected = '/route_tests/view/2-another-title';
		$this->assertEquals($result, $expected);
	}

	public function testGetSlugs() {
		$SluggableRoute = new SluggableRoute(null, null, null);

		$results = $SluggableRoute->getSlugs($this->RouteTest->alias);
		$expected = array(
			1 => 'a-page-title',
			2 => 'another-title',
			3 => 'i-love-cakephp',
		);
		$this->assertEquals($results, $expected);

		unset($SluggableRoute->RouteTest_slugs);
		Cache::clear(false, 'Slugger');
		$results = $SluggableRoute->getSlugs($this->RouteTest->alias, 'name');
		$expected = array(
			1 => 'page-title',
			2 => 'routing-is-fun',
			3 => 'cake-rocks',
		);
		$this->assertEquals($results, $expected);
	}

	public function testSlug() {
		$SluggableRoute = new SluggableRoute(null, null, null);

		$slug = array(
			'_pk' => 1,
			'_field' => 'Page Title',
			'_count' => 1,
		);
		$result = $SluggableRoute->slug($slug);
		$expected = 'page-title';
		$this->assertEquals($result, $expected);

		$slug = array(
			'_pk' => 1,
			'_field' => 'Routing is fun!',
			'_count' => 1,
		);
		$result = $SluggableRoute->slug($slug);
		$expected = 'routing-is-fun';
		$this->assertEquals($result, $expected);

		// check for duplicates
		$slug = array(
			'_pk' => 1,
			'_field' => 'Page Title',
			'_count' => 3,
		);
		$result = $SluggableRoute->slug($slug);
		$expected = '1-page-title';
		$this->assertEquals($result, $expected);

		// check non-ascii chars
		$slug = array(
			'_pk' => 1,
			'_field' => 'ñice Pagé!',
			'_count' => 3,
		);
		$result = $SluggableRoute->slug($slug);
		$expected = '1-nice-page';
		$this->assertEquals($result, $expected);
	}

	public function testMatch() {
		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 1
		));
		$expected = '/route_tests/view/a-page-title';
		$this->assertEquals($result, $expected);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 2
		));
		$expected = '/route_tests/view/another-title';
		$this->assertEquals($result, $expected);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 2,
			'passedVar'
		));
		$expected = '/route_tests/view/passedVar/another-title';
		$this->assertEquals($result, $expected);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 5
		));
		$expected = '/route_tests/view/RouteTest:5';
		$this->assertEquals($result, $expected);
	}

	public function testParse() {
		$result = Router::parse('/route_tests/view/another-title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTest' => 2
			),
			'plugin' => null,
			'pass' => array()
		);
		$this->assertEquals($result, $expected);

		$result = Router::parse('/route_tests/view/missing-title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(),
			'plugin' => null,
			'pass' => array('missing-title')
		);
		$this->assertEquals($result, $expected);

		$result = Router::parse('/route_tests/view/passedVar/another-title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTest' => 2
			),
			'plugin' => null,
			'pass' => array('passedVar')
		);
		$this->assertEquals($result, $expected);
	}

	public function testDuplicateSlug() {
		$this->RouteTest->create();
		$this->RouteTest->save(array(
			'title' => 'A page title',
			'name' => 'Page Title',
		));

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 1
		));
		$expected = '/route_tests/view/1-a-page-title';
		$this->assertEquals($result, $expected);

		$result = Router::parse('/route_tests/view/a-page-title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(),
			'plugin' => null,
			'pass' => array('a-page-title')
		);
		$this->assertEquals($result, $expected);

		$result = Router::parse('/route_tests/view/1-a-page-title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTest' => 1
			),
			'plugin' => null,
			'pass' => array()
		);
		$this->assertEquals($result, $expected);

		$id = $this->RouteTest->id;
		$result = Router::parse('/route_tests/view/'.$id.'-a-page-title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTest' => $id
			),
			'plugin' => null,
			'pass' => array()
		);
		$this->assertEquals($result, $expected);
	}

	public function testSlugField() {
		Router::reload();
		Router::connect('/:controller/:action/*',
			array(),
			array(
				'routeClass' => 'SluggableRoute',
				'models' => array('RouteTest' => 'name')
			)
		);

		$result = Router::url(array(
			'controller' => 'route_tests',
			'action' => 'view',
			'RouteTest' => 3
		));
		$expected = '/route_tests/view/cake-rocks';
		$this->assertEquals($result, $expected);

		$result = Router::parse('/route_tests/view/routing-is-fun');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTest' => 2
			),
			'plugin' => null,
			'pass' => array()
		);
		$this->assertEquals($result, $expected);
	}

	public function testMultipleModels() {
		Router::reload();
		Router::connect('/:controller/:action/*',
			array(),
			array(
				'routeClass' => 'SluggableRoute',
				'models' => array('RouteTest', 'RouteTwoTest')
			)
		);

		$result = Router::parse('/route_tests/view/another-title');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTest' => 2
			),
			'plugin' => null,
			'pass' => array()
		);
		$this->assertEquals($result, $expected);

		$result = Router::parse('/route_tests/view/my-blog-post');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(
				'RouteTwoTest' => 1
			),
			'plugin' => null,
			'pass' => array()
		);
		$this->assertEquals($result, $expected);
	}

	public function testMissingModel() {
		Router::connect('/:controller/:action/*',
			array(),
			array(
				'routeClass' => 'SluggableRoute',
				'models' => array('UndefinedModel')
			)
		);

		$result = Router::parse('/route_tests/view/my-slug');
		$expected = array(
			'controller' => 'route_tests',
			'action' => 'view',
			'named' => array(),
			'plugin' => null,
			'pass' => array('my-slug')
		);
		$this->assertEquals($result, $expected);
	}

}

?>