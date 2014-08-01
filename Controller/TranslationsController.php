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

App::uses('I18nAppController', 'I18n.Controller');

/**
 * I18n TranslationsController
 *
 * @package I18n.Controller
 */
class TranslationsController extends I18nAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Translations';

/**
 * Helpers
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Form'
	);

/**
 * Fields to preset in search forms
 *
 * @var array
 * @see Search.PrgComponent
 */
	public $presetVars = array(
		array('field' => 'locale', 'type' => 'value'),
		array('field' => 'model', 'type' => 'value'),
		array('field' => 'field', 'type' => 'value'),
		array('field' => 'content', 'type' => 'value'),
	);

/**
 * Models to use with this controller
 *
 * @var array
 */
	public $uses = array(
		'I18n.Translation'
	);

/**
 * Constructor
 *
 * @param CakeRequest $request Request object for this controller. Can be null for testing,
 *  but expect that features that use the request parameters will not work.
 * @param CakeResponse $response Response object for this controller.
 */
	public function __construct($request, $response) {
		$this->_setupComponents();
		parent::__construct($request, $response);
	}

/**
 * Setup components based on plugin availability
 *
 * @return void
 * @link https://github.com/CakeDC/search
 */
	protected function _setupComponents() {
		if ($this->_pluginLoaded('Search', false)) {
			$this->components[] = 'Search.Prg';
		}
	}

/**
 * Wrapper for CakePlugin::loaded()
 *
 * @throws MissingPluginException
 * @param string $plugin
 * @param boolean $exceiption
 * @return boolean
 */
	protected function _pluginLoaded($plugin, $exception = true) {
		$result = CakePlugin::loaded($plugin);
		if ($exception === true && $result === false) {
			throw new MissingPluginException(array('plugin' => $plugin));
		}
		return $result;
	}

/**
 * Admin index for translation.
 * 
 * @access public
 */
	public function admin_index() {
		if ($this->_pluginLoaded('Search', false)) {
			$this->Prg->commonProcess();
			$conditions = $this->Translation->parseCriteria($this->passedArgs);
		} else {
			$conditions= array();
		}
		$this->Paginator->settings = array(
			'search',
			'conditions' => $conditions
		);
		$this->set('translations', $this->Paginator->paginate());
	}

/**
 * Admin view for translation.
 *
 * @param string $id, translation id 
 * @access public
 */
	public function admin_view($id = null) {
		try {
			$translation = $this->Translation->view($id);
		} catch (OutOfBoundsException $e) {
			$this->Session->setFlash($e->getMessage());
			$this->redirect(array('action' => 'index'));
		}
		$this->set(compact('translation'));
	}

/**
 * Admin add for translation.
 * 
 * @access public
 */
	public function admin_add() {
		try {
			$result = $this->Translation->add($this->data);
			if ($result === true) {
				$this->Session->setFlash(__('The translation has been saved', true));
				$this->redirect(array('action' => 'index'));
			}
		} catch (OutOfBoundsException $e) {
			$this->Session->setFlash($e->getMessage());
		} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin edit for translation.
 *
 * @param string $id, translation id 
 * @access public
 */
	public function admin_edit($id = null) {
		try {
			$result = $this->Translation->edit($id, $this->data);
			if ($result === true) {
				$this->Session->setFlash(__('Translation saved', true));
				$this->redirect(array('action' => 'view', $this->Translation->data['Translation']['id']));
			} else {
				$this->data = $result;
			}
		} catch (OutOfBoundsException $e) {
			$this->Session->setFlash($e->getMessage());
			$this->redirect('/');
		}
	}

/**
 * Admin edit for translation.
 *
 * @param string $id, translation id 
 * @access public
 */
	public function admin_edit_multi($model, $foreignKey) {
		$locales = Configure::read('Config.locales.available');
		$this->set(compact('model', 'foreignKey', 'locales'));
		try {
			$result = $this->Translation->edit_multi($model, $foreignKey, $this->data);
			if ($result === true) {
				$this->Session->setFlash(__('Translation saved', true));
			} else {
				$this->set('translations', $result);
				$this->data = $result;
			}
		} catch (OutOfBoundsException $e) {
			$this->Session->setFlash($e->getMessage());
		}
	}

/**
 * Admin delete for translation.
 *
 * @param string $id, translation id 
 * @access public
 */
	public function admin_delete($id = null) {
		try {
			$result = $this->Translation->validateAndDelete($id, $this->data);
			if ($result === true) {
				$this->Session->setFlash(__('Translation deleted', true));
				$this->redirect(array('action' => 'index'));
			}
		} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->Translation->data['translation'])) {
			$this->set('translation', $this->Translation->data['translation']);
		}
	}
}
