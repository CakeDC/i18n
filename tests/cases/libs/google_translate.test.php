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

// Import required classes
App::import('Lib', array('I18n.GoogleTranslate'));
App::import('Core', 'HttpSocket');

// Generate mocks
Mock::generate('HttpSocket');

/**
 * Google Translate Library test case
 *
 * @package i18n
 * @subpackage i18n.tests.libs
 */
class GoogleTranslateTestCase extends CakeTestCase {

/**
 * Google translate library instance
 * 
 * @var GoogleTranslate
 */
	public $GoogleTranslate;

/**
 * Instance of the Mocked Http socket
 * 
 * @var MockHttpSocket
 */
	public $Http = null;

/**
 * Internal configuration to know whether the Socket must be mocked or not
 * 
 * @var boolean
 */
	private $__mockSocket = false;

/**
 * startTest method
 *
 * @return void
 */
	public function startTest() {
		$this->GoogleTranslate = new GoogleTranslate();
		if ($this->__mockSocket) {
			$this->GoogleTranslate->key = 'myApiKey';
			$this->GoogleTranslate->Http = $this->Http = new MockHttpSocket();
		}
	}

/**
 * end the test and reset the environment
 *
 * @return void
 */
	public function endTest() {
		unset($this->GoogleTranslate, $this->Http);
	}

/**
 * Test the translate API call
 *
 * @return void
 */
	public function testTranslate() {
		$result = $this->GoogleTranslate->translate('Bonjour', 'fr', 'en');
		if ($this->__mockSocket) {
			$expected = array(
				'q' => 'Bonjour',
				'langpair' => 'fr|en',
				'format' => 'text',
				'v' => '1.0',
				'key' => 'myApiKey');
			$this->Http->expectOnce('post', array('http://ajax.googleapis.com/ajax/services/language/', $expected));
			$this->Http->setReturnValue('post', '{"responseData": {"translatedText":"Hello"}, "responseDetails": null, "responseStatus": 200}');
			$this->Http->response['status']['code'] = 200;
		}
		$this->assertEqual($result, 'Hello');
	}
	
/**
 * Test the translate API call with Html content
 *
 * @return void
 */
	public function testTranslateHtml() {
		$result = $this->GoogleTranslate->translate('<strong>Bonjour</strong>', 'fre', 'en', true);
		if ($this->__mockSocket) {
			$expected = array(
				'q' => '<strong>Bonjour</strong>',
				'langpair' => 'fr|en',
				'format' => 'html',
				'v' => '1.0',
				'key' => 'myApiKey');
			$this->Http->expectOnce('post', array('http://ajax.googleapis.com/ajax/services/language/', $expected));
			$this->Http->setReturnValue('post', '{"responseData": {"translatedText":"<strong>Hello</strong>"}, "responseDetails": null, "responseStatus": 200}');
			$this->Http->response['status']['code'] = 200;
		}
		$this->assertEqual($result, '<strong>Hello</strong>');
	}
}
