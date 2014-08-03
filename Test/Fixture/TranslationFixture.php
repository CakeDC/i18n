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

class TranslationFixture extends CakeTestFixture {

/**
 * Name
 *
 * @var string
 * @access public
 */
	public $name = 'Translation';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'locale' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'field' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 64, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			//'I18N_LOCALE_FIELD' => array('column' => array('locale', 'model', 'foreign_key', 'field'), 'unique' => 1),
			//'I18N_LOCALE_ROW' => array('column' => array('locale', 'model', 'foreign_key'), 'unique' => 0),
			//'I18N_LOCALE_MODEL' => array('column' => array('locale', 'model'), 'unique' => 0), 'I18N_FIELD' => array('column' => array('model', 'foreign_key', 'field'), 'unique' => 0),
			//'I18N_ROW' => array('column' => array('model', 'foreign_key'), 'unique' => 0)
		),
		'tableParameters' => array(
			'charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM'
		)
	);

/**
 * Records
 *
 * @var array
 * @access public
 */
	public $records = array(
		array(
			'id' => 'translate-1',
			'locale' => 'eng',
			'model' => 'Article',
			'foreign_key' => 'article-1',
			'field' => 'title',
			'content' => 'sample content.'
		),
	);

}
