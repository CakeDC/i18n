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

class TranslationsControllerTestCase extends CakeTestCase {

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = array();

/**
 * Start Test callback
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Translations = new TranslationsController();
		$this->Translations->constructClasses();
		$this->Translations->Prg->initialize($this->Translations);
		$this->Translations->params = array(
			'named' => array(),
			'pass' => array(),
			'url' => array());
		$fixture = new I18nFixture();
		$this->record = array('Translation' => $fixture->records[0]);
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
 * Convenience method to assert Flash messages
 *
 * @param string $message
 * @return void
 */
	public function assertFlash($message) {
		$flash = $this->Translations->Session->read('Message.flash');
		$this->assertEquals($flash['message'], $message);
		$this->Translations->Session->delete('Message.flash');
	}

/**
 * Test object instances
 *
 * @return void
 * @access public
 */
	public function testInstance() {
		$this->assertTrue($this->Translations, 'TranslationsController');
		$this->assertTrue($this->Translations->Translation, 'Translation');
	}

/**
 * testAdminIndex
 *
 * @return void
 * @access public
 */
	public function testAdminIndex() {
		$this->Translations->admin_index();
		$this->assertTrue(!empty($this->Translations->viewVars['translations']));
	}

/**
 * testAdminAdd
 *
 * @return void
 * @access public
 */
	public function testAdminAdd() {
		$this->Translations->data = $this->record;
		$this->Translations->data['Translation']['foreign_key'] = 'article-2';
		unset($this->Translations->data['Translation']['id']);
		$this->Translations->admin_add();
		$this->Translations->expectRedirect(array('action' => 'index'));
		$this->assertFlash('The translation has been saved');
		$this->Translations->expectExactRedirectCount();
	}

/**
 * testAdminEdit
 *
 * @return void
 * @access public
 */
	public function testAdminEdit() {
		$this->Translations->admin_edit('translation-1');
		$this->assertEquals($this->Translations->data['Translation'], $this->record['Translation']);

		$this->Translations->data = $this->record;
		$this->Translations->admin_edit('translation-1');
		$this->Translations->expectRedirect(array('action' => 'view', 1));
		$this->assertFlash('Translation saved');
		$this->Translations->expectExactRedirectCount();
	}

/**
 * testAdminView
 *
 * @return void
 */
	public function testAdminView() {
		$this->Translations->admin_view('translation-1');
		$this->assertTrue(!empty($this->Translations->viewVars['translation']));

		$this->Translations->admin_view('WRONG-ID');
		$this->Translations->expectRedirect(array('action' => 'index'));
		$this->assertFlash('Invalid Translation');
		$this->Translations->expectExactRedirectCount();
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->Translations->admin_delete('WRONG-ID');
		$this->Translations->expectRedirect(array('action' => 'index'));
		$this->assertFlash('Invalid Translation');

		$this->Translations->admin_delete('translation-1');
		$this->assertTrue(!empty($this->Translations->viewVars['translation']));

		$this->Translations->data = array('Translation' => array('confirmed' => 1));
		$this->Translations->admin_delete('translation-1');
		$this->Translations->expectRedirect(array('action' => 'index'));
		$this->assertFlash('Translation deleted');
		$this->Translations->expectExactRedirectCount();
	}
}
