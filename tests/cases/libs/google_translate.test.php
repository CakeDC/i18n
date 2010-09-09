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
	private $__mockSocket = true;

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
		if ($this->__mockSocket) {
			$this->GoogleTranslate->useUserIp = false;
			$expected = array(
				'langpair' => 'fr|en',
				'format' => 'text',
				'q' => 'Bonjour',
				'v' => '1.0',
				'key' => 'myApiKey');
			$this->Http->expectOnce('post', array('http://ajax.googleapis.com/ajax/services/language/translate', $expected));
			$this->Http->setReturnValue('post', '{"responseData": {"translatedText":"Hello"}, "responseDetails": null, "responseStatus": 200}');
			$this->Http->response['status']['code'] = 200;
		}
		$result = $this->GoogleTranslate->translate('Bonjour', 'fr', 'en');
		$this->assertEqual($result, 'Hello');
	}
	
/**
 * Test the translate API call with Html content
 *
 * @return void
 */
	public function testTranslateHtml() {
		if ($this->__mockSocket) {
			$this->GoogleTranslate->useUserIp = false;
			$expected = array(
				'langpair' => 'fr|en',
				'format' => 'html',
				'q' => '<strong>Bonjour</strong>',
				'v' => '1.0',
				'key' => 'myApiKey');
			$this->Http->expectOnce('post', array('http://ajax.googleapis.com/ajax/services/language/translate', $expected));
			$this->Http->setReturnValue('post', '{"responseData": {"translatedText":"<strong>Hello</strong>"}, "responseDetails": null, "responseStatus": 200}');
			$this->Http->response['status']['code'] = 200;
		}
		$result = $this->GoogleTranslate->translate('<strong>Bonjour</strong>', 'fre', 'en', true);
		$this->assertEqual($result, '<strong>Hello</strong>');
	}

/**
 * Test the translate API call with a text longer than the Google Translate API limit (5000 chars)
 *
 * @return void
 */
	public function testTranslateLongText() {
		$lipsum = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ';
		$text = 'Bonjour ... ';
		$expectedTranslation = 'Hello ... ';
		while (strlen($text) <= 6000) {
			$text .= $lipsum;
			$expectedTranslation .= $lipsum;
		}
		$expectedTranslation = trim($expectedTranslation);

		if ($this->__mockSocket) {
			// Expects a first call with "Bonjour" and as many lipsums as needed to fill the 5000 chars
			$this->GoogleTranslate->useUserIp = false;
			$expectedText = 'Bonjour ... ';
			$response = 'Hello ... ';
			while(strlen($expectedText) + strlen($lipsum) < 5000) {
				$expectedText .= $lipsum;
				$response .= $lipsum;
			}

			$expected = array(
				'langpair' => 'fr|en',
				'format' => 'text',
				'q' => $expectedText,
				'v' => '1.0',
				'key' => 'myApiKey');
			$this->Http->expectAt(0, 'post', array('http://ajax.googleapis.com/ajax/services/language/translate', $expected));
			$this->Http->setReturnValueAt(0, 'post', '{"responseData": {"translatedText":"' . trim($response) . '"}, "responseDetails": null, "responseStatus": 200}');

			// Expects a second call with the remaining lipsums filling the 6000 total chars
			$limit = 6000 - strlen($expectedText);
			$expectedText = '';
			while(strlen($expectedText) <= $limit) {
				$expectedText .= $lipsum;
			}
			$expected['q'] = $expectedText;
			$this->Http->expectAt(1, 'post', array('http://ajax.googleapis.com/ajax/services/language/translate', $expected));
			$this->Http->setReturnValueAt(1, 'post', '{"responseData": {"translatedText":"' . trim($expectedText) . '"}, "responseDetails": null, "responseStatus": 200}');
			$this->Http->response['status']['code'] = 200;
		}
		
		$result = $this->GoogleTranslate->translate($text, 'fr', 'en');
		$this->assertEqual($result, $expectedTranslation);
	}
}
