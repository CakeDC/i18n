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
 * Spy Google Translate class that allows to access protected methods for our tests
 *
 */
class SpyGoogleTranslate extends GoogleTranslate {
	public function splitText($text, $maxLength, $html = false) {
		return $this->_splitText($text, $maxLength, $html);
	}
	public function isTranslatable($text, $html) {
		return $this->_isTranslatable($text, $html);
	}
}

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
 * @var SpyGoogleTranslate
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
		$this->GoogleTranslate = new SpyGoogleTranslate();
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

/**
 * Test splitting a text that contains sentences longer than the limit
 *
 * @return void
 */
	public function testSplitLongSentences() {
		$text = 'This is a text, without any correct sentence - I hate using a period. Sometimes I don\'t!';
		$result = $this->GoogleTranslate->splitText($text, 30);
		$expected = array(
			'This is a text, without any ',
			'correct sentence - I hate ',
			'using a period. ',
			'Sometimes I don\'t!');
		$this->assertEqual($result, $expected);
	}

/**
 * Test splitting a text that contains html code
 *
 * @return void
 */
	public function testSplitHtml() {
		$text = '
			<h1>Hello</h1>
			<p>Sentence one. And two.</p>
			<p>This is a text with HTML code.</p>
			<code><?php echo "test"; ?></code>';
		$result = $this->GoogleTranslate->splitText($text, 30, true);
		$expected = array(
			'<h1>Hello</h1>',
			'<p>Sentence one. And two.</p>',
			'<p>',
			'This is a text with HTML code.',
			'</p>',
			'<code>',
			'<?php echo "test"; ?>'
				. "\n", // Added during the call to $node->C14N(); in HtmlTokenizer
			'</code>');
		$this->assertEqual($result, $expected);

		$text = '
			<h1>Hello</h1>
			<p>This is a text with <strong>more difficult</strong>HTML code.</p>
			<br />
			<p>And other things: <a href="http://google.com">Google</a>
				<img src="http://google.com/ing.png" /> for instance.</p>';
		$result = $this->GoogleTranslate->splitText($text, 40, true);
		$expected = array(
			'<h1>Hello</h1>',
			'<p>',
			'This is a text with ',
			'<strong>more difficult</strong>',
			'HTML code.',
			'</p>',
			'<br />',
			'<p>',
			'And other things: ',
			'<a href="http://google.com">Google</a>',
			'<img src="http://google.com/ing.png">',
			' for instance.',
			'</p>'
		);
		$this->assertEqual($result, $expected);
	
		$text = '
			<code><?php echo "foobar"; ?></code>
			A text here
			<code><script language="javascript">alert("Hello world!");</script></code>
			<p>This is a paragraph</p>';
		$result = $this->GoogleTranslate->splitText($text, 140, true);
		$expected = array(
			'<code><?php echo "foobar"; ?></code>
			A text here
			<code><script language="javascript">alert("Hello world!");</script></code>',
			'<p>This is a paragraph</p>'
		);
		$this->assertEqual($result, $expected);
	}

/**
 * Test isTranslatable protected method
 *
 * @return void
 */
	public function testIsTranslatable() {
		$texts = array(
			'<h1>Hello</h1>',
			'<p>Sentence one. And two.</p>',
			'<p>',
			'This is a text with HTML code.',
			'</p>',
			'<code><?php echo "test"; ?></code>');
		$results = $resultsNotHtml = array();
		foreach ($texts as $text) {
			$resultsNotHtml[] = $this->GoogleTranslate->isTranslatable($text, false);
			$results[] = $this->GoogleTranslate->isTranslatable($text, true);
		}
		$this->assertEqual($resultsNotHtml, array(true, true, true, true, true, true));
		$this->assertEqual($results, array(true, true, false, true, false, false));
	}
}
