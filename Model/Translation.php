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

App::uses('I18nAppModel', 'I18n.Model');

class Translation extends I18nAppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'Translation';

/**
 * model table
 *
 * @var string $name
 * @access public
 */
	public $useTable = 'i18n';

/**
 * Validation parameters - initialized in constructor
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'locale' => array(
			'notempty' => array('rule' => array('notempty'), 'required' => true, 'allowEmpty' => false, 'message' => 'Please enter a Locale')),
		'model' => array(
			'notempty' => array('rule' => array('notempty'), 'required' => true, 'allowEmpty' => false, 'message' => 'Please enter a Model')),
		'foreign_key' => array(
			'notempty' => array('rule' => array('notempty'), 'required' => true, 'allowEmpty' => false, 'message' => 'Please enter a Foreign Key')),
		'field' => array(
			'notempty' => array('rule' => array('notempty'), 'required' => true, 'allowEmpty' => false, 'message' => 'Please enter a Field')),
	);

/**
 * Filter args attribute to be used by the Searchable behavior
 *
 * @var array
 * @access public
 */
	public $filterArgs = array(
		array('name' => 'locale', 'type' => 'like'),
		array('name' => 'model', 'type' => 'like'),
		array('name' => 'field', 'type' => 'like'),
		array('name' => 'content', 'type' => 'like')
	);

/**
 * Constructor
 *
 * @param bool|string $id ID
 * @param string $table Table
 * @param string $ds Datasource
 */
	public function __construct($id = false, $table = null, $ds = null) {
		$this->_setupBehaviors();
		parent::__construct($id, $table, $ds);
	}

/**
 * Setup available plugins
 *
 * This checks for the existence of certain plugins, and if available, uses them.
 *
 * @return void
 * @link https://github.com/CakeDC/search
 * @link https://github.com/CakeDC/utils
 */
	protected function _setupBehaviors() {
		if (CakePlugin::loaded('Search') && class_exists('SearchableBehavior')) {
			$this->actsAs[] = 'Search.Searchable';
		}
	}

/**
 * List of valid finder method options, supplied as the first parameter to find().
 *
 * @var array
 */
	public $findMethods = array(
		'search' => true
	);

/**
 * Adds a new record to the database
 *
 * @param array post data, should be Contoller->data
 * @return array
 */
	public function add($data = null) {
		if (!empty($data)) {
			$this->create();
			$result = $this->save($data);
			if ($result !== false) {
				$this->data = array_merge($data, $result);
				return true;
			} else {
				throw new OutOfBoundsException(__('Could not save the translation, please check your inputs.', true));
			}
			return $return;
		}
	}

/**
 * Edits an existing Translation.
 *
 * @param string $id, translation id
 * @param array $data, controller post data usually $this->data
 * @return mixed True on successfully save else post data as array
 * @throws OutOfBoundsException If the element does not exists
 * @access public
 */
	public function edit($id = null, $data = null) {
		$translation = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
				)));

		if (empty($translation)) {
			throw new OutOfBoundsException(__('Invalid Translation', true));
		}
		$this->set($translation);

		if (!empty($data)) {
			$this->set($data);
			$result = $this->save(null, true);
			if ($result) {
				$this->data = $result;
				return true;
			} else {
				return $data;
			}
		} else {
			return $translation;
		}
	}

/**
 * Edits an existing Translation.
 *
 * @param string $id, translation id
 * @param array $data, controller post data usually $this->data
 * @return mixed True on successfully save else post data as array
 * @throws OutOfBoundsException If the element does not exists
 * @access public
 */
	public function edit_multi($model, $foreignKey, $data = null) {
		$translations = $this->find('all', array(
			'order' => array('locale'),
			'conditions' => array(
				"{$this->alias}.model" => $model,
				"{$this->alias}.foreign_key" => $foreignKey,
			)
		));

		if (empty($translations)) {
			throw new OutOfBoundsException(__('Invalid Translation', true));
		}

		// @@todo code here
		// $this->set($translation);

		if (!empty($data[$this->alias])) {
			foreach ($data[$this->alias] as $locale => $fields) {
				foreach ($fields as $field => $_data) {
					if (!empty($_data['id'])) {
						$record = $this->read(null, $_data['id']);
						$record[$this->alias]['content'] = $_data['content'];
						$this->save($record);
					} else {
						$record = array('Translation' => array(
							'model' => $model,
							'foreign_key' => $foreignKey,
							'locale' => $locale,
							'field' => $field,
							'content' => $_data['content']));
							$this->create($record);
							$this->save($record);
					}
				}
			}
			return true;
		} else {
			return $translations;
		}
	}

/**
 * Returns the record of a Translation.
 *
 * @param string $id, translation id.
 * @return array
 * @throws OutOfBoundsException If the element does not exists
 */
	public function view($id = null) {
		$translation = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id)));

		if (empty($translation)) {
			throw new OutOfBoundsException(__('Invalid Translation', true));
		}

		return $translation;
	}

/**
 * Validates the deletion
 *
 * @param string $id, translation id
 * @param array $data, controller post data usually $this->data
 * @return boolean True on success
 * @throws OutOfBoundsException If the element does not exists
 */
	public function validateAndDelete($id = null, $data = array()) {
		$translation = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
			))
		);

		if (empty($translation)) {
			throw new OutOfBoundsException(__('Invalid Translation', true));
		}

		$this->data['translation'] = $translation;
		if (!empty($data)) {
			$data['Translation']['id'] = $id;
			$tmp = $this->validate;
			$this->validate = array(
				'id' => array('rule' => 'notEmpty'),
				'confirm' => array('rule' => '[1]'));

			$this->set($data);
			if ($this->validates()) {
				if ($this->delete($data['Translation']['id'])) {
					return true;
				}
			}
			$this->validate = $tmp;
			throw new Exception(__('You need to confirm to delete this Translation', true));
		}
	}

/**
 * Perform search request
 *
 * @param string $state
 * @param array $query
 * @param array $results
 * @return array
 */
	protected function _findSearch($state, $query, $results = array()) {
		if ($state == 'before') {
			//$query = Set::merge($defaults, $query);
			if (!empty($query['operation']) && $query['operation'] === 'count') {
				unset($query['limit']);
				$query = $this->_findCount('before', $query, $results);
			}
			return $query;

		} elseif ($state == 'after') {
			if (isset($query['operation']) && $query['operation'] == 'count') {
				$results = $this->_findCount('after', $query, $results);
			}
			return $results;
		}
	}
}
