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

class I18nFixture extends CakeTestFixture {

/**
 * Name
 *
 * @var string
 * @access public
 */
	public $name = 'I18n';

/**
 * Name
 *
 * @var string
 * @access public
 */
	public $table = 'I18n';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'locale' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 64),
		'foreign_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'field' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 64),
		'content' => array('type' => 'text', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'I18N_LOCALE_FIELD' => array('column' => array('locale', 'model', 'foreign_key', 'field'), 'unique' => 1),
			'I18N_LOCALE_ROW' => array('column' => array('locale', 'model', 'foreign_key'), 'unique' => 0),
			'I18N_LOCALE_MODEL' => array('column' => array('locale', 'model'), 'unique' => 0),
			'I18N_FIELD' => array('column' => array('model', 'foreign_key', 'field'), 'unique' => 0),
			'I18N_ROW' => array('column' => array('model', 'foreign_key'), 'unique' => 0)
		),
		'tableParameters' => array(
			'engine' => 'MyISAM'
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
			'id' => 'translation-1',
			'locale' => 'eng',
			'model' => 'Article',
			'foreign_key' => 'article-1',
			'field' => 'title',
			'content' => 'sample content.'
		)
	);

}
