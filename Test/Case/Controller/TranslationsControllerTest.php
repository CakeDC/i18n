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
App::uses('TranslationsController', 'I18n.Controller');
App::uses('I18nFixture', 'I18n.Test/Fixture');
App::uses('CakeResponse', 'Network');


/**
 * TestTranslationController
 *
 * @package translations
 * @subpackage translations.tests.cases.controllers
 */
class TestTranslationController extends TranslationsController {

/**
 * Auto render
 *
 * @var boolean
 */
	public $autoRender = false;

/**
 * Redirect URL
 *
 * @var mixed
 */
	public $redirectUrl = null;

/**
 * Override controller method for testing
 *
 * @return void
 */
	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

/**
 * Override controller method for testing
 *
 * @return void
 */
	public function render($action = null, $layout = null, $file = null) {
		$this->renderedView = $action;
	}
}

/**
 * TranslationControllerTest
 *
 * @package translation
 * @subpackage translation.tests.cases.controllers
 */
class TranslationControllerTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.i18n.i18n'
	);

/**
 * Translations Controller Instance
 *
 * @return void
 */
	public $Translations = null;

/**
 * Start Test callback
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Translations = $this->__buildTranslationInstance();
		$this->Translations->constructClasses();
		$this->Translations->Prg->initialize($this->Translations);
		$this->Translations->params = array(
			'named' => array(),
			'pass' => array(),
			'url' => array());
		$this->Translations->Session = $this->getMock('SessionComponent', array(), array(), '', false);
		$fixture = new I18nFixture();
		$this->record = array('Translation' => $fixture->records[0]);
	}

/**
 * builder for TestTranslationController
 *
 * @return TestTranslationController
 */
	private function __buildTranslationInstance() {
		$request = new CakeRequest();
		$response = new CakeResponse();
		return new TestTranslationController($request, $response);
	}

/**
 * End Test callback
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Translations);
		ClassRegistry::flush();
	}

/**
 * Test object instances
 *
 * @return void
 * @access public
 */
	public function testInstance() {
		$this->assertInstanceOf('TranslationsController', $this->Translations);
		$this->assertInstanceOf('Translation', $this->Translations->Translation);
	}

/**
 * testAdminIndex
 *
 * @covers TranslationsController::admin_index
 * @return void
 */
	public function testAdminIndex() {
		$this->Translations->admin_index();
		$this->assertTrue(!empty($this->Translations->viewVars['translations']));
	}

/**
 * testAdminAdd
 *
 * @covers TranslationsController::admin_add
 * @return void
 */
	public function testAdminAdd() {
		$this->record['Translation']['foreign_key'] = 'article-2';
		unset($this->record['Translation']['id']);
		$this->Translations->data = $this->record;
		$this->Translations->Session->expects($this->at(0))
			->method('setFlash')
			->with($this->equalTo(__('The translation has been saved', true)))
			->will($this->returnValue(true));

		$this->Translations->admin_add();
		$this->assertEquals($this->Translations->redirectUrl, array('action' => 'index'));
	}

/**
 * testAdminEdit
 *
 * @covers TranslationsController::admin_edit
 * @return void
 */
	public function testAdminEdit() {
		$this->Translations->admin_edit('translation-1');
		$this->assertEquals($this->Translations->data, $this->record);

		$this->record['Translation']['content'] = 'Example Content';
		$this->Translations->data = $this->record;
		$this->Translations->Session->expects($this->at(0))
			->method('setFlash')
			->with($this->equalTo(__('Translation saved', true)))
			->will($this->returnValue(true));

		$this->Translations->admin_edit('translation-1');
		$result = $this->Translations->Translation->find('first', array(
			'conditions' => array(
				'Translation.id' => $this->Translations->data['Translation']['id']
			)
		));

		$this->assertEquals('Example Content', $result['Translation']['content']);
		$this->assertEquals($this->Translations->redirectUrl, array('action' => 'view', $this->Translations->data['Translation']['id']));
	}

/**
 * testAdminView
 *
 * @covers TranslationsController::admin_view
 * @return void
 */
	public function testAdminView() {
		$this->Translations->admin_view('translation-1');
		$this->assertTrue(!empty($this->Translations->viewVars['translation']));
		$this->assertEquals($this->record, $this->Translations->viewVars['translation']);

		$this->Translations->admin_view('invalid-translation');
		$this->assertEquals($this->Translations->redirectUrl, array('action' => 'index'));
	}

/**
 * testAdminDelete
 *
 * @covers TranslationsController::admin_delete
 * @return void
 */
	public function testAdminDelete() {
		$this->Translations->Session->expects($this->at(0))
			->method('setFlash')
			->with($this->equalTo('Invalid Translation'))
			->will($this->returnValue(true));

		$this->Translations->admin_delete('invalid-translation');
		$this->assertEquals($this->Translations->redirectUrl, array('action' => 'index'));

		$this->Translations->Session->expects($this->any())
			->method('setFlash')
			->with($this->equalTo(__('Translation deleted', true)))
			->will($this->returnValue(true));
		$this->Translations->data = $this->record;
		$this->Translations->admin_delete('translation-1');
		$this->assertEquals($this->Translations->redirectUrl, array('action' => 'index'));
	}

/**
 * testEditMulti
 *
 * @covers TranslationsController::admin_edit_multi
 * @return void
 */
	public function testAdminEditMulti() {
		$this->Translations->data = array('Translation' => array($this->record));
		$this->Translations->Session->expects($this->any())
			->method('setFlash')
			->with($this->equalTo(__('Translation saved', true)))
			->will($this->returnValue(true));
		$this->Translations->admin_edit_multi('Article', 'article-1');
		$this->assertEquals('Article', $this->Translations->viewVars['model']);
		$this->assertEquals('article-1', $this->Translations->viewVars['foreignKey']);
	}

/**
 * testEditMulti
 *
 * @covers TranslationsController::admin_edit_multi
 * @return void
 */
	public function testAdminEditMultiEmptyData() {
		$this->Translations->data = array();
		$this->Translations->admin_edit_multi('Article', 'article-1');
		$expected = array(
			array(
				'Translation' => array(
					'id' => 'translation-1',
					'locale' => 'eng',
					'model' => 'Article',
					'foreign_key' => 'article-1',
					'field' => 'title',
					'content' => 'sample content.'
				)
			)
		);

		$this->assertEquals($expected, $this->Translations->viewVars['translations']);
		$this->assertEquals($expected, $this->Translations->data);
	}

/**
 * testEditMulti
 *
 * @covers TranslationsController::admin_edit_multi
 * @return void
 */
	public function testAdminEditMultiInvalid() {
		$this->Translations->Session->expects($this->at(0))
			->method('setFlash')
			->with($this->equalTo(__('Invalid Translation', true)))
			->will($this->returnValue(true));
		$this->Translations->admin_edit_multi('invalid-model', 'invalid-foreign-key');
	}
}