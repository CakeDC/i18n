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

/**
 * i18n Migration 001
 *
 * @package i18n
 * @subpackage i18n.config.migrations
 */
class I18nMigration001 extends CakeMigration {
	
/**
 * Migration array
 * 
 * @var array $migration
 */ 
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'i18n' => array(
					'id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
					'locale' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 3, 'key' => 'index'),
					'model' => array('type'=>'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'length' => 64),
					'foreign_key' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36),
					'field' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 64),
					'content' => array('type'=>'text', 'null' => true, 'default' => NULL),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'I18N_LOCALE_FIELD' => array('column' => array('locale', 'model', 'foreign_key', 'field'), 'unique' => 1),
						'I18N_LOCALE_ROW' => array('column' => array('locale', 'model', 'foreign_key'), 'unique' => 0),
						'I18N_LOCALE_MODEL' => array('column' => array('locale', 'model'), 'unique' => 0),
						'I18N_FIELD' => array('column' => array('model', 'foreign_key', 'field'), 'unique' => 0),
						'I18N_ROW' => array('column' => array('model', 'foreign_key'), 'unique' => 0)),
				),
			)
		),
		'down' => array(
			'drop_table' => array('i18n')
		)
	);

/**
 * before migration callback
 *
 * @param string $direction, up or down direction of migration process
 */
	public function before($direction) {
		return true;
	}

/**
 * after migration callback
 *
 * @param string $direction, up or down direction of migration process
 */
	public function after($direction) {
		return true;
	}
}
