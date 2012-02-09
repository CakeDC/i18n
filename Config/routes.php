<?php
	App::uses('I18nRoute', 'I18n.Routing/Route');
	$prefixes = Router::prefixes();
	$options = array('routeClass' => 'I18nRoute');

	if ($plugins = CakePlugin::loaded()) {
		foreach ($plugins as $key => $value) {
			$plugins[$key] = Inflector::underscore($value);
		}
		$pluginPattern = implode('|', $plugins);
		$match = array('plugin' => $pluginPattern) + $options;

		foreach ($prefixes as $prefix) {
			$params = array('prefix' => $prefix, $prefix => true);
			$indexParams = $params + array('action' => 'index');
			Router::connect("/{$prefix}/:plugin/:controller", $indexParams, $match);
			Router::connect("/{$prefix}/:plugin/:controller/:action/*", $params, $match);
		}
		Router::connect('/:plugin/:controller', array('action' => 'index'), $match);
		Router::connect('/:plugin/:controller/:action/*', array(), $match);
	}


	foreach ($prefixes as $prefix) {
		$params = array('prefix' => $prefix, $prefix => true);
		$indexParams = $params + array('action' => 'index');
		Router::connect("/{$prefix}/:controller", $indexParams, $options);
		Router::connect("/{$prefix}/:controller/:action/*", $params, $options);
	}

	Router::connect('/:controller', array('action' => 'index', 'lang' => DEFAULT_LANGUAGE), $options);
	Router::connect('/:controller/:action/*', array('lang' => DEFAULT_LANGUAGE), $options);

	Router::connect('/:controller', array('action' => 'index'), $options);
	Router::connect('/:controller/:action/*', array(), $options);

	$namedConfig = Router::namedConfig();
	if ($namedConfig['rules'] === false) {
		Router::connectNamed(true);
	}

	foreach (Router::$routes as $i => &$route) {
		if (!empty($route->options['__promote'])) {
			$r = Router::$routes[$i - 1];
			Router::$routes[$i - 1] = $route;
			Router::$routes[$i] = $r;
			unset($route->options['__promote']);
		}
	}

	unset($namedConfig, $params, $indexParams, $prefix, $prefixes, $options, $plugins, $pluginPattern, $match, $value, $route, $r, $i);