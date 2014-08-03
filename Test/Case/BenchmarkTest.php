<?php
/**
 * This case is here solely to do some rudimentary and possibly inaccurate
 * benchmarking of the route's caching and slugging ability
 */

App::uses('SluggableRoute', 'I18n.Routing/Route');
App::uses('Router', 'Routing');

class BenchmarkTestCase extends CakeTestCase {

	public $fixtures = array(
		'plugin.i18n.route_test'
	);

	public function startTest() {
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

	public function endTest() {
		Cache::clear(false, 'Slugger');
		Router::reload();
		unset($this->RouteTest);
	}

	public function tearDown() {
		Cache::clear(false, 'Slugger');
	}

	public function testCakeCacheBenefit() {
		$records = 1000;
		$iterations = 1000;
		$this->_insertABillionRecords($records);
		$speed1 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times before cache: $speed1 s");
		$speed2 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times using var cache: $speed2 s");
		debug('Speed increase: '.(round($speed1/$speed2*100)-100).'%');
		$this->_clearVarCache();
		$speed3 = $this->_iterate($iterations);		
		debug("Routed $records records $iterations times new request, using cache: $speed3 s");
		debug('Speed increase: '.(round($speed1/$speed3*100)-100).'%');
	}

	public function testCakeCacheBenefit2() {
		$records = 100;
		$iterations = 10000;
		$this->_insertABillionRecords($records);
		$speed1 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times before cache: $speed1 s");
		$speed2 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times using var cache: $speed2 s");
		debug('Speed increase: '.(round($speed1/$speed2*100)-100).'%');
		$this->_clearVarCache();
		$speed3 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times new request, using cache: $speed3 s");
		debug('Speed increase: '.(round($speed1/$speed3*100)-100).'%');
	}

	public function testCakeCacheBenefit3() {
		$records = 10000;
		$iterations = 1000;
		$this->_insertABillionRecords($records);
		$speed1 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times before cache: $speed1 s");
		$speed2 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times using var cache: $speed2 s");
		debug('Speed increase: '.(round($speed1/$speed2*100)-100).'%');
		$this->_clearVarCache();
		$speed3 = $this->_iterate($iterations);
		debug("Routed $records records $iterations times new request, using cache: $speed3 s");
		debug('Speed increase: '.(round($speed1/$speed3*100)-100).'%');
	}

	function _clearVarCache() {
		unset(Router::$routes[0]->RouteTest_slugs);
	}

	function _iterate($count = 1) {
		$starttime = microtime(true);
		for ($i=0; $i<$count/2; $i++) {
			Router::url(array(
				'controller' => 'route_tests',
				'action' => 'view',
				'RouteTest' => 42
			));
			Router::parse('/route_tests/view/42-some-route');
		}
		$endtime = microtime(true);
		return $endtime-$starttime;
	}

	public function _insertABillionRecords($count) {
		for ($i=0; $i<$count; $i++) {
			$this->RouteTest->create();
			$this->RouteTest->save(array(
				'RouteTest' => array(
					'title' => 'some route',
					'name' => 'bench this!',
				)
			));
		}
	}

}