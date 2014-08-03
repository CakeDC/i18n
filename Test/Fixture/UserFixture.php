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
 * i18n base model
 *
 * @package 	i18n
 * @subpackage  i18n.tests.fixtures
 */
class UserFixture extends CakeTestFixture {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'User';

/**
 * Table
 *
 * @var array $table
 * @access public
 */
	public $table = 'users';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false, 'default' => null),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null),
		'passwd' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128),
		'password_token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128),
		'email' => array('type' => 'string', 'null' => true, 'default' => null),
		'email_authenticated' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'email_token' => array('type' => 'string', 'null' => true, 'default' => null),
		'email_token_expires' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'tos' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'last_activity' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_login' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'is_admin' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'role' => array('type' => 'string', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
			'id' => 'user-1',
			'username' => 'phpnut',
			'slug' => 'phpnut',
			'passwd' => 'test', // test
			'password_token' => 'testtoken',
			'email' => 'larry.masters@cakedc.com',
			'email_authenticated' => 1,
			'email_token' => 'testtoken',
			'email_token_expires' => '2008-03-25 02:45:46',
			'tos' => 1,
			'active' => 1,
			'last_activity' => '2008-03-25 02:45:46',
			'last_login' => '2008-03-25 02:45:46',
			'is_admin' => 1,
			'role' => 'admin',
			'created' => '2008-03-25 02:45:46',
			'modified' => '2008-03-25 02:45:46'
		),
	);

}