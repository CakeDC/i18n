<?php
/* Translation Test cases generated on: 2011-09-28 12:09:49 : 1317214069*/
App::import('Model', 'Translation');

App::import('Lib', 'Templates.AppTestCase');
class TranslationTestCase extends AppTestCase {
/**
 * Autoload entrypoint for fixtures dependecy solver
 *
 * @var string
 * @access public
 */
	public $plugin = 'i18n';

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
 * @param string $method
 * @return void
 * @access public
 */
	public function startTest($method) {
		parent::startTest($method);
		$this->Translation = AppMock::getTestModel('I18n.Translation');
		$fixture = new I18nFixture();
		$this->record = array('Translation' => $fixture->records[0]);
	}

/**
 * End Test callback
 *
 * @param string $method
 * @return void
 * @access public
 */
	public function endTest($method) {
		parent::endTest($method);
		unset($this->Translation);
		ClassRegistry::flush();
	}

/**
 * Test validation rules
 *
 * @return void
 * @access public
 */
	public function testValidation() {
		$this->assertValid($this->Translation, $this->record);

		// Test mandatory fields
		$data = array('Translation' => array('id' => 'new-id'));
		$expectedErrors = array('locale', 'model', 'foreign_key', 'field'); // TODO Update me with mandatory fields
		$this->assertValidationErrors($this->Translation, $data, $expectedErrors);
	}

/**
 * Test adding a Translation 
 *
 * @return void
 * @access public
 */
	public function testAdd() {
		$data = $this->record;
		unset($data['Translation']['id']);
		$data['Translation']['foreign_key'] = 'article-2';
		$result = $this->Translation->add($data);
		$this->assertTrue($result);
		
		try {
			$data = $this->record;
			unset($data['Translation']['id']);
			unset($data['Translation']['field']);
			$result = $this->Translation->add($data);
			$this->fail('No exception');
		} catch (OutOfBoundsException $e) {
			$this->pass('Correct exception thrown');
		}
		
	}

/**
 * Test editing a Translation 
 *
 * @return void
 * @access public
 */
	public function testEdit() {
		$result = $this->Translation->edit('translation-1', null);

		$expected = $this->Translation->read(null, 'translation-1');
		$this->assertEqual($result['Translation'], $expected['Translation']);

		// put invalidated data here
		$data = $this->record;
		$data['Translation']['field'] = null;

		$result = $this->Translation->edit('translation-1', $data);
		$this->assertEqual($result, $data);

		$data = $this->record;

		$result = $this->Translation->edit('translation-1', $data);
		$this->assertTrue($result);

		$result = $this->Translation->read(null, 'translation-1');

		try {
			$this->Translation->edit('wrong_id', $data);
			$this->fail('No exception');
		} catch (OutOfBoundsException $e) {
			$this->pass('Correct exception thrown');
		}
	}

/**
 * Test viewing a single Translation 
 *
 * @return void
 * @access public
 */
	public function testView() {
		$result = $this->Translation->view('translation-1');
		$this->assertTrue(isset($result['Translation']));
		$this->assertEqual($result['Translation']['id'], 'translation-1');

		try {
			$result = $this->Translation->view('wrong_id');
			$this->fail('No exception on wrong id');
		} catch (OutOfBoundsException $e) {
			$this->pass('Correct exception thrown');
		}
	}

/**
 * Test ValidateAndDelete method for a Translation 
 *
 * @return void
 * @access public
 */
	public function testValidateAndDelete() {
		try {
			$postData = array();
			$this->Translation->validateAndDelete('invalidTranslationId', $postData);
		} catch (OutOfBoundsException $e) {
			$this->assertEqual($e->getMessage(), 'Invalid Translation');
		}
		try {
			$postData = array(
				'Translation' => array(
					'confirm' => 0));
			$result = $this->Translation->validateAndDelete('translation-1', $postData);
		} catch (Exception $e) {
			$this->assertEqual($e->getMessage(), 'You need to confirm to delete this Translation');
		}

		$postData = array(
			'Translation' => array(
				'confirm' => 1));
		$result = $this->Translation->validateAndDelete('translation-1', $postData);
		$this->assertTrue($result);
	}
	
}