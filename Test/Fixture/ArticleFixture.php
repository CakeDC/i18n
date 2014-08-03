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
class ArticleFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'AnotherArticle'
 * @access public
 */
	public $name = 'Article';

/**
 * fields property
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type'=>'string', 'null' => false, 'length' => 36, 'key' => 'primary'), 
		'title' => array('type' => 'string', 'null' => false),
		'language_id' => array('type' => 'string', 'null' => true),
		'created' => array('type' => 'datetime'),
		'updated' => array('type' => 'datetime')
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	public $records = array(
		array(
			'id' => 'article-1', 
			'title' => 'First Article', 
			'language_id' => 'eng',
			'created' => '2007-03-18 10:39:23', 
			'updated' => '2007-03-18 10:41:31'),
		array(
			'id' => 'article-2', 
			'title' => 'First Article', 
			'language_id' => 'fra',
			'created' => '2007-03-18 10:39:23', 
			'updated' => '2007-03-18 10:41:31'),
	);

}

?>