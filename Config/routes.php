<?php
	App::uses('I18nRoute', 'I18n.Routing/Route');
	$prefixes = Router::prefixes();
	$options = array('routeClass' => 'I18nRoute');

	foreach ($prefixes as $prefix) {
		$params = array('prefix' => $prefix, $prefix => true);
		$indexParams = $params + array('action' => 'index');
		Router::connect("/{$prefix}/:controller", $indexParams, $options);
		Router::connect("/{$prefix}/:controller/:action/*", $params, $options);
	}
	Router::connect('/:controller', array('action' => 'index'), $options);
	Router::connect('/:controller/:action/*', array(), $options);

	$namedConfig = Router::namedConfig();
	if ($namedConfig['rules'] === false) {
		Router::connectNamed(true);
	}

	unset($namedConfig, $params, $indexParams, $prefix, $prefixes, $options);
