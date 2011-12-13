<?php

class I18nSchema extends CakeSchema {
	public $name = 'I18n';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $i18n = array(
			'id' => array('type'=>'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
			'locale' => array('type'=>'string', 'null' => false, 'length' => 3, 'key' => 'index'),
			'model' => array('type'=>'string', 'null' => false, 'key' => 'index'),
			'foreign_key' => array('type'=>'string', 'null' => false, 'length' => 36),
			'field' => array('type'=>'string', 'null' => false, 'length' => 64),
			'content' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'I18N_LOCALE_FIELD' => array('column' => array('locale', 'model', 'foreign_key', 'field'), 'unique' => 1), 'I18N_LOCALE_ROW' => array('column' => array('locale', 'model', 'foreign_key', 'field', 'locale', 'model', 'foreign_key'), 'unique' => 0), 'I18N_LOCALE_MODEL' => array('column' => array('locale', 'model', 'foreign_key', 'field', 'locale', 'model', 'foreign_key', 'locale', 'model'), 'unique' => 0), 'I18N_FIELD' => array('column' => array('locale', 'model', 'foreign_key', 'field', 'locale', 'model', 'foreign_key', 'locale', 'model', 'model', 'foreign_key', 'field'), 'unique' => 0), 'I18N_ROW' => array('column' => array('locale', 'model', 'foreign_key', 'field', 'locale', 'model', 'foreign_key', 'locale', 'model', 'model', 'foreign_key', 'field', 'model', 'foreign_key'), 'unique' => 0))
		);
}
?>