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
App::uses('Model', 'Model');

/**
 * i18n behavior tests
 *
 * @package 	i18n
 * @subpackage  i18n.tests.cases.behaviors
 */
class I18nableTestCase extends CakeTestCase { 

/**
 * Fixtures used in the SessionTest
 *
 * @var array
 * @access public
 */
	public $fixtures = array(
		'plugin.i18n.article'
	);

/**
 * startTest
 *
 * @return void
 * @access public
 */
	public function setUp() {
		$this->Article = ClassRegistry::init('I18n.Article');
		$this->Article->Behaviors->attach('I18n.I18nable');
	}

/**
 * endTest
 *
 * @return void
 * @access public
 */
	public function tearDown() {
		unset($this->Article);
	}

	public function testSave() {
		Configure::write('Config.language', 'eng');
		$article = array(
			'title' => 'New Article');
		$this->Article->create($article);
		$result = $this->Article->save();
		$this->assertTrue(!empty($result));
		$this->assertEquals($result['Article']['language_id'], 'eng');
	}

	public function testFind() {
		Configure::write('Config.language', 'eng');
		$articles = $this->Article->find('all');
		$this->assertEquals(count($articles), 1);
		$this->assertEquals($articles[0]['Article']['id'], 'article-1');

		$articles = $this->Article->find('all', array('language' => 'fra'));
		$this->assertEquals(count($articles), 1);
		$this->assertEquals($articles[0]['Article']['id'], 'article-2');

		Configure::write('Config.language', 'fra');
		$articles = $this->Article->find('all');
		$this->assertEquals(count($articles), 1);
		$this->assertEquals($articles[0]['Article']['id'], 'article-2');

		$articles = $this->Article->find('all', array('ignoreLanguage' => true));
		$this->assertEquals(count($articles), 2);
	}

}
