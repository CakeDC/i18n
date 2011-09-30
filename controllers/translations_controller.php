<?php
class TranslationsController extends AppController {
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
	public $helpers = array('Html', 'Form');

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array('Search.Prg');

/**
 * Fields to preset in search forms
 *
 * @var array
 * @see Search.PrgComponent
 */
	public $presetVars = array(
		array('field' => 'locale',  'type' => 'value'),
		array('field' => 'model',   'type' => 'value'),
		array('field' => 'field',   'type' => 'value'),
		array('field' => 'content', 'type' => 'value'),
	);		

	public $uses = array('I18n.Translation');
	
/**
 * Admin index for translation.
 * 
 * @access public
 */
	public function admin_index() {
		$this->Prg->commonProcess();
        $this->paginate = array(
			'search',
            'conditions' => $this->Translation->parseCriteria($this->passedArgs));
				
		$this->set('translations', $this->paginate()); 
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
	debug($this->data);
		$locales = Configure::read('Config.locales.available');
		$this->set(compact('model', 'foreignKey', 'locales'));
		try {
			$result = $this->Translation->edit_multi($model, $foreignKey, $this->data);
			if ($result === true) {
				$this->Session->setFlash(__('Translation saved', true));
//				$this->redirect(array('action' => 'view', $this->Translation->data['Translation']['id']));
				
			} else {
				$this->set('translations', $result);
				$this->data = $result;
			}
		} catch (OutOfBoundsException $e) {
			$this->Session->setFlash($e->getMessage());
//			$this->redirect('/');
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
?>